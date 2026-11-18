"""
Рисует confusion matrix как SVG без matplotlib — картинка, но без ~30-40 МБ
зависимостей (matplotlib + pillow + fonttools + kiwisolver + contourpy),
которые при ~25 КБ/с в этом окружении стоили бы лишние 20-25 минут.
SVG — валидный формат картинки (открывается в браузере и вставляется в
README/страницу отчёта), поэтому требованию «сохрани как картинку» это
не противоречит.
"""

CELL = 64
MARGIN_LEFT = 90
MARGIN_TOP = 70
MARGIN_RIGHT = 30
MARGIN_BOTTOM = 90


def _color_for(value: int, max_value: int) -> str:
    """Синяя заливка: чем больше значение в ячейке, тем темнее."""
    if max_value <= 0:
        t = 0.0
    else:
        t = value / max_value
    # от почти белого (t=0) до насыщенного синего (t=1)
    r = round(235 - t * (235 - 30))
    g = round(242 - t * (242 - 64))
    b = round(255 - t * (255 - 175))
    return f"rgb({r},{g},{b})"


def render_confusion_matrix_svg(matrix: list[list[int]], labels: list[str], out_path) -> None:
    n = len(labels)
    max_value = max((v for row in matrix for v in row), default=0)

    width = MARGIN_LEFT + CELL * n + MARGIN_RIGHT
    height = MARGIN_TOP + CELL * n + MARGIN_BOTTOM

    parts = [
        f'<svg xmlns="http://www.w3.org/2000/svg" width="{width}" height="{height}" '
        f'viewBox="0 0 {width} {height}" font-family="Arial, Helvetica, sans-serif">',
        f'<rect width="{width}" height="{height}" fill="white" />',
        f'<text x="{width / 2}" y="24" text-anchor="middle" font-size="16" font-weight="bold">'
        f"Confusion matrix (test)</text>",
        f'<text x="{width / 2}" y="42" text-anchor="middle" font-size="12" fill="#555">'
        f"предсказанный уровень &#8594;</text>",
    ]

    # Подпись оси Y (повёрнутая)
    y_label_x = 18
    y_label_y = MARGIN_TOP + CELL * n / 2
    parts.append(
        f'<text x="{y_label_x}" y="{y_label_y}" text-anchor="middle" font-size="12" fill="#555" '
        f'transform="rotate(-90 {y_label_x} {y_label_y})">настоящий уровень &#8594;</text>'
    )

    # Заголовки колонок (предсказано)
    for j, label in enumerate(labels):
        x = MARGIN_LEFT + j * CELL + CELL / 2
        parts.append(f'<text x="{x}" y="{MARGIN_TOP - 10}" text-anchor="middle" font-size="13" font-weight="bold">{label}</text>')

    # Строки: подпись + ячейки
    for i, row_label in enumerate(labels):
        y = MARGIN_TOP + i * CELL
        parts.append(
            f'<text x="{MARGIN_LEFT - 12}" y="{y + CELL / 2 + 5}" text-anchor="end" '
            f'font-size="13" font-weight="bold">{row_label}</text>'
        )
        for j, value in enumerate(matrix[i]):
            x = MARGIN_LEFT + j * CELL
            fill = _color_for(value, max_value)
            text_color = "white" if max_value and value / max_value > 0.6 else "#222"
            on_diagonal = i == j
            stroke = "#4f46e5" if on_diagonal else "#ddd"
            stroke_width = 2 if on_diagonal else 1
            parts.append(
                f'<rect x="{x}" y="{y}" width="{CELL}" height="{CELL}" fill="{fill}" '
                f'stroke="{stroke}" stroke-width="{stroke_width}" />'
            )
            parts.append(
                f'<text x="{x + CELL / 2}" y="{y + CELL / 2 + 5}" text-anchor="middle" '
                f'font-size="14" fill="{text_color}">{value}</text>'
            )

    axis_y = MARGIN_TOP + CELL * n + 30
    parts.append(
        f'<text x="{width / 2}" y="{axis_y}" text-anchor="middle" font-size="11" fill="#777">'
        f"строка = настоящий уровень, столбец = предсказанный; диагональ выделена рамкой</text>"
    )

    parts.append("</svg>")

    out_path = str(out_path)
    with open(out_path, "w", encoding="utf-8") as f:
        f.write("\n".join(parts))
