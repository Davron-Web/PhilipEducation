<?php

namespace Database\Seeders;

use App\Models\Content\GrammarTopic;
use App\Models\Content\Lesson;
use App\Models\System\Level;
use Illuminate\Database\Seeder;

class GrammarTopicSeeder extends Seeder
{
    /**
     * Each entry links a grammar topic to the lesson that already teaches
     * it (and already has real practice exercises attached), so the public
     * grammar page can surface genuine tasks instead of duplicating content.
     */
    public function run(): void
    {
        // Remove placeholder/test rows left over from manually testing the
        // admin CRUD form (empty or "dd"/"updated theory text" content).
        GrammarTopic::query()->delete();
        Lesson::query()->update(['grammar_topic_id' => null]);

        $levels = Level::pluck('id', 'code');

        foreach ($this->topics() as $data) {
            $levelId = $levels[$data['level']] ?? null;
            if (! $levelId) {
                continue;
            }

            $topic = GrammarTopic::create([
                'title' => $data['title'],
                'category' => $data['category'] ?? null,
                'level_id' => $levelId,
                'theory' => $data['theory'],
            ]);

            Lesson::where('level_id', $levelId)
                ->where('title', $data['lesson'])
                ->update(['grammar_topic_id' => $topic->id]);
        }
    }

    /**
     * @return array<int, array{level: string, lesson: string, title: string, category: string, theory: string}>
     */
    private function topics(): array
    {
        return [
            // A1
            [
                'level' => 'A1',
                'lesson' => 'Present Simple Basics',
                'title' => 'Present Simple',
                'category' => 'Tenses',
                'theory' => "Present Simple — базовое время для описания того, что происходит регулярно, всегда или является общеизвестным фактом, а не единичного действия в моменте.\n\nКогда используется:\n— привычки и регулярные действия: I go to the gym three times a week.\n— факты и общие истины: The Earth orbits the Sun.\n— расписания и графики: The film starts at 8 p.m.\n— постоянные состояния и мнения (глаголы состояния: know, believe, like, want, own): I know her well. She likes jazz.\n— последовательность действий (инструкции, рассказ анекдота, спортивный комментарий): First you boil the water, then you add the pasta.\n\nСтруктура — утверждение:\nI/You/We/They + глагол в начальной форме; He/She/It + глагол + -s (-es после -s/-sh/-ch/-x/-o; -y меняется на -ies после согласной).\nI work. He works. She watches TV. They study English. It flies south in winter.\n\nСтруктура — отрицание:\nI/You/We/They + do not (don't) + начальная форма; He/She/It + does not (doesn't) + начальная форма.\nI don't like coffee. He doesn't work on Sundays. They don't live here anymore.\n\nСтруктура — вопрос:\nDo/Does + подлежащее + начальная форма глагола? В специальных вопросах вопросительное слово ставится перед Do/Does.\nDo you speak French? Does she play the piano? Where do you live? What time does the shop open?\n\nПримеры:\nI always wake up at 7 a.m. My sister works as a nurse. Water boils at 100 degrees Celsius. We usually spend Christmas with our grandparents. Does he drive to work, or does he take the bus? The museum closes at 5 p.m. on weekdays.\n\nДополнительные примеры:\nCats sleep about 15 hours a day. I don't eat meat — I'm vegetarian. Do you understand the question? The train to London leaves every hour. She never checks her email at weekends. Plants need sunlight to grow.\n\nЧастые ошибки:\n— Пропуск -s в 3-м лице: 'She work here' — неправильно, нужно 'She works here'.\n— Использование -s после do/does: 'He doesn't works' — неправильно, нужно 'He doesn't work' (глагол без -s после doesn't).\n— Present Simple вместо Present Continuous для действия прямо сейчас: 'I read a book now' — неправильно, нужно 'I am reading a book now'.",
            ],
            [
                'level' => 'A1',
                'lesson' => 'Verb To Be: Am, Is, Are',
                'title' => 'Глагол to be (am/is/are)',
                'category' => 'Verbs and Voices',
                'theory' => "Глагол to be — один из самых важных глаголов в английском языке. Он используется для описания состояния, качеств, профессии, возраста.\n\n"
                    ."I am (I'm), He/She/It is (He's/She's/It's), You/We/They are (You're/We're/They're).\n\n"
                    ."Утверждение: I am a student. She is happy. They are teachers.\n"
                    ."Отрицание: am/is/are + not.\n"
                    ."Пример: I am not tired. He is not (isn't) at home.\n"
                    ."Вопрос: Am/Is/Are + подлежащее?\n"
                    ."Пример: Are you ready? Is she from Spain?\n\n"
                    ."Важно: to be НЕ используется вместе с обычным смысловым глаголом в этой же роли — 'I am like' неправильно, нужно просто 'I like'.",
            ],
            [
                'level' => 'A1',
                'lesson' => 'Present Continuous',
                'title' => 'Present Continuous',
                'category' => 'Tenses',
                'theory' => "Present Continuous описывает действие в развитии — то, что происходит прямо сейчас или в текущий, ограниченный по времени период, а не постоянную характеристику.\n\nКогда используется:\n— действие, происходящее в момент речи: She is talking on the phone right now.\n— временная ситуация, которая не обязательно происходит именно в эту секунду, но актуальна в этот период: I am reading a great book these days (не читаю прямо сейчас, но в целом сейчас читаю).\n— запланированное действие в ближайшем будущем с указанием времени: We are meeting Tom at 6 p.m. tomorrow.\n— описание меняющейся, развивающейся ситуации: The climate is changing rapidly.\n— раздражение от повторяющегося действия (с always): He is always losing his keys!\n\nСтруктура — утверждение:\nПодлежащее + am/is/are + глагол-ing.\nI am working. She is cooking dinner. They are watching a film. It is raining.\n\nСтруктура — отрицание:\nПодлежащее + am/is/are + not + глагол-ing.\nI am not working today. He isn't listening. We aren't going to the party.\n\nСтруктура — вопрос:\nAm/Is/Are + подлежащее + глагол-ing?\nAre you listening to me? Is she coming with us? What are you doing tonight?\n\nПримеры:\nLook! It's snowing outside. I'm currently learning to drive. My parents are staying with us this week. Why are you crying? The company is growing very fast this year. We are having dinner — can I call you back?\n\nДополнительные примеры:\nShe's studying for her exams all week. Are you still living in that flat downtown? The children are playing in the garden. I'm not working tomorrow, so let's meet up. He is always interrupting people — it's so annoying! They're planning a trip to Italy next month.\n\nЧастые ошибки:\n— Continuous с глаголами состояния: 'I am knowing the answer' — неправильно, нужно 'I know the answer' (know, like, want, believe, own обычно не используются в Continuous).\n— Забытая форма to be: 'She cooking dinner' — неправильно, нужно 'She is cooking dinner'.\n— Present Continuous для регулярной привычки: 'I am going to the gym every day' лучше заменить на Present Simple 'I go to the gym every day', если речь о привычке, а не о временном периоде.",
            ],
            [
                'level' => 'A1',
                'lesson' => 'Past Simple Basics',
                'title' => 'Past Simple',
                'category' => 'Tenses',
                'theory' => "Past Simple используется, когда действие полностью завершилось в прошлом в определённый, обычно указанный момент времени.\n\nКогда используется:\n— единичное завершённое действие в прошлом: She called me yesterday.\n— последовательность событий в прошлом: He woke up, had breakfast, and left for work.\n— привычка или повторяющееся действие в прошлом, которое больше не происходит: I played football every weekend when I was a child.\n— исторический факт: World War II ended in 1945.\n\nСтруктура — утверждение:\nПодлежащее + глагол-ed (правильные глаголы) или особая форма (неправильные глаголы).\nI worked late last night. She went to Spain in July. They saw the accident.\n\nСтруктура — отрицание:\nПодлежащее + did not (didn't) + начальная форма глагола (независимо от лица).\nI didn't finish the report. He didn't call me back. We didn't know about the meeting.\n\nСтруктура — вопрос:\nDid + подлежащее + начальная форма глагола?\nDid you see that film? Did she pass the exam? What time did the train leave?\n\nПримеры:\nWe visited my grandparents last weekend. I didn't sleep well last night. Did you enjoy the concert? Shakespeare wrote many famous plays. She lost her phone on the bus yesterday. They arrived late because of the traffic.\n\nДополнительные примеры:\nI bought a new laptop two days ago. He didn't answer my question. Where did you go on holiday last year? The shop closed at 9 p.m. yesterday. My father taught me how to swim when I was five. We didn't have enough time to finish the test.\n\nЧастые ошибки:\n— Окончание -ed в вопросах и отрицаниях: 'Did you saw him?' — неправильно, нужно 'Did you see him?' (после did — начальная форма, без -ed).\n— Неправильные глаголы с -ed: 'I goed there' — неправильно, нужно 'I went there' (go — неправильный глагол).\n— Past Simple без указания конкретного времени, когда важнее результат — тогда лучше Present Perfect: 'I have lost my keys' (не знаю где) vs 'I lost my keys yesterday' (когда именно).",
            ],
            [
                'level' => 'A1',
                'lesson' => 'Articles: A, An, The',
                'title' => 'Артикли a/an/the',
                'category' => 'Determiners',
                'theory' => "В английском языке два вида артиклей: неопределённый (a/an) и определённый (the).\n\n"
                    ."A используется перед словом, начинающимся с согласного звука: a book, a car.\n"
                    ."An используется перед словом, начинающимся с гласного звука: an apple, an hour.\n"
                    ."A/an ставится, когда мы говорим о предмете впервые или не уточняем, какой именно: I saw a dog in the park.\n\n"
                    ."The ставится, когда речь идёт о конкретном, уже известном предмете: The dog I saw yesterday was brown.\n"
                    ."The также используется с уникальными объектами (the sun, the moon) и во второй раз, когда предмет уже упоминался.\n\n"
                    .'Артикль часто не нужен: с неисчисляемыми существительными в общем смысле (I like music), с именами собственными (London), во множественном числе в общем смысле (Dogs are loyal).',
            ],

            // A2
            [
                'level' => 'A2',
                'lesson' => 'Past Continuous Tense',
                'title' => 'Past Continuous',
                'category' => 'Tenses',
                'theory' => "Past Continuous описывает действие, которое было в процессе в определённый момент прошлого — часто как фон для другого, более короткого действия.\n\nКогда используется:\n— длительное действие в конкретный момент прошлого: At 8 p.m. yesterday, I was having dinner.\n— фоновое действие, которое прервало другое, короткое действие: I was walking home when it started to rain.\n— два параллельных длительных действия в прошлом: While she was cooking, he was setting the table.\n— вежливое, смягчённое упоминание намерения: I was wondering if you could help me.\n\nСтруктура — утверждение:\nПодлежащее + was/were + глагол-ing.\nI was reading. They were playing football. It was raining heavily.\n\nСтруктура — отрицание:\nПодлежащее + was/were + not + глагол-ing.\nShe wasn't listening. We weren't expecting visitors.\n\nСтруктура — вопрос:\nWas/Were + подлежащее + глагол-ing?\nWere you sleeping when I called? What was she doing at that time?\n\nПримеры:\nI was watching TV when the power went out. They were arguing loudly when I walked in. While I was studying, my brother was playing video games. She wasn't paying attention during the lesson. What were you doing at midnight last night? It was getting dark, so we decided to go home.\n\nДополнительные примеры:\nWe were driving to the airport when the accident happened. I was just thinking about you! He was working late every night that month. Were they waiting long before the doctor arrived? The children were laughing and shouting in the yard. I wasn't feeling well, so I stayed home.\n\nЧастые ошибки:\n— Past Continuous для одного короткого, завершённого действия: 'I was breaking the glass' обычно неверно для случайного одномоментного действия — лучше Past Simple 'I broke the glass'.\n— Пропуск was/were: 'She cooking dinner when I arrived' — неправильно, нужно 'She was cooking dinner when I arrived'.\n— Two Past Simple actions вместо Continuous+Simple, когда одно действие явно длиннее другого и служит фоном: 'When the phone rang, I cooked' лучше заменить на 'When the phone rang, I was cooking'.",
            ],
            [
                'level' => 'A2',
                'lesson' => 'Future Simple: Will',
                'title' => 'Future Simple (will)',
                'category' => 'Tenses',
                'theory' => "Future Simple с will описывает будущее без заранее продуманного плана: спонтанные решения, предположения, обещания и предложения помощи.\n\nКогда используется:\n— спонтанное решение в момент речи: The phone is ringing — I'll answer it.\n— предположение о будущем без явных доказательств (часто с think, probably, maybe): I think it will rain tomorrow.\n— обещание: I will always love you.\n— предложение помощи или готовность что-то сделать: I'll carry that bag for you.\n— факт о будущем, который мы не контролируем: The sun will rise at 6:03 tomorrow.\n\nСтруктура — утверждение:\nПодлежащее + will ('ll) + начальная форма глагола (одна форма для всех лиц).\nI will call you. She'll be there soon. They will win the match.\n\nСтруктура — отрицание:\nПодлежащее + will not (won't) + начальная форма глагола.\nI won't tell anyone. He won't be happy about this.\n\nСтруктура — вопрос:\nWill + подлежащее + начальная форма глагола?\nWill you help me with this? What will happen next?\n\nПримеры:\nI'll call you when I get home. Don't worry, everything will be fine. Will you marry me? I don't think she'll come to the party. It will probably take two hours. We'll see what happens.\n\nДополнительные примеры:\nI'm sure you'll pass the exam. Will you be at the meeting tomorrow? I won't forget your birthday. The train will arrive in ten minutes. She'll help you if you ask her. I promise I'll be there on time.\n\nЧастые ошибки:\n— will для заранее спланированного намерения вместо going to: 'I decided yesterday — I will buy a new car' лучше 'I'm going to buy a new car' (план уже принят заранее).\n— will после if в условной части (First Conditional): 'If it will rain, I'll stay home' — неправильно, нужно 'If it rains, I'll stay home'.\n— Двойное сокращение willn't вместо won't: правильная отрицательная форма — won't, а не willn't.",
            ],
            [
                'level' => 'A2',
                'lesson' => 'Present Perfect: Introduction',
                'title' => 'Present Perfect (введение)',
                'category' => 'Tenses',
                'theory' => "Present Perfect связывает прошлое действие с настоящим моментом: важен либо результат сейчас, либо сам факт опыта, а не то, когда именно это произошло.\n\nКогда используется:\n— результат прошлого действия виден или важен сейчас: I've lost my keys (сейчас у меня их нет).\n— жизненный опыт без указания точного времени: I have visited Japan twice.\n— действие, начавшееся в прошлом и продолжающееся до сих пор (с for/since): I have lived here for ten years.\n— недавнее действие (с just, recently): She has just finished her homework.\n— неоконченный период времени (today, this week, this year): I haven't seen him today.\n\nСтруктура — утверждение:\nПодлежащее + have/has + глагол в 3-й форме (Participle II).\nI have finished. She has arrived. They have already eaten.\n\nСтруктура — отрицание:\nПодлежащее + have/has + not (haven't/hasn't) + глагол в 3-й форме.\nI haven't seen that film. He hasn't called yet.\n\nСтруктура — вопрос:\nHave/Has + подлежащее + глагол в 3-й форме?\nHave you ever been to Egypt? Has she finished the report?\n\nПримеры:\nI have already had lunch. Have you ever ridden a horse? She has worked here since 2018. We haven't decided yet. He has just left the office. They have never tried sushi.\n\nДополнительные примеры:\nI have read that book three times. Has the package arrived yet? My sister has just passed her driving test. We have known each other for years. I haven't finished the project, so I can't send it. Have you seen my glasses anywhere?\n\nЧастые ошибки:\n— Present Perfect с конкретным прошедшим временем: 'I have seen him yesterday' — неправильно, yesterday требует Past Simple: 'I saw him yesterday'.\n— Путаница for/since: 'I have lived here since five years' — неправильно, since указывает точку отсчёта, а for — период: 'I have lived here for five years' / 'since 2019'.\n— Ever/never не в вопросе/отрицании: 'I ever went to Rome' — неправильно (ever здесь не нужен); лучше 'I have been to Rome' или в вопросе 'Have you ever been to Rome?'.",
            ],
            [
                'level' => 'A2',
                'lesson' => 'First Conditional',
                'title' => 'First Conditional (реальное условие)',
                'category' => 'Sentences',
                'theory' => "First Conditional используется для реальных, возможных ситуаций в будущем и их вероятных последствий.\n\n"
                    ."Формула: If + Present Simple, ... will + глагол.\n"
                    ."Пример: If it rains, I will stay at home. If you study hard, you will pass the exam.\n\n"
                    ."Условная часть (if-clause) может стоять и после главной части, тогда запятая не нужна:\n"
                    ."I will stay at home if it rains.\n\n"
                    ."Вместо will можно использовать can, may, might для выражения возможности:\n"
                    ."If you finish early, you can go home.\n\n"
                    .'Важно: в условной части (после if) никогда не используется will, только Present Simple, даже если речь о будущем.',
            ],
            [
                'level' => 'A2',
                'lesson' => 'Comparatives and Superlatives',
                'title' => 'Сравнительная и превосходная степень',
                'category' => 'Adjectives',
                'theory' => "Сравнительная степень (comparative) используется для сравнения двух предметов, превосходная (superlative) — для выделения одного предмета среди многих.\n\n"
                    ."Короткие прилагательные (1 слог): + -er / + -est.\n"
                    ."Пример: tall → taller → the tallest.\n\n"
                    ."Прилагательные из 2+ слогов: more / the most.\n"
                    ."Пример: beautiful → more beautiful → the most beautiful.\n\n"
                    ."Исключения: good → better → the best; bad → worse → the worst; far → further → the furthest.\n\n"
                    ."Сравнение двух предметов: than.\n"
                    ."Пример: This book is more interesting than that one.\n"
                    ."Превосходная степень: the + прилагательное-est/most + существительное.\n"
                    ."Пример: This is the most interesting book I've ever read.",
            ],

            // B1
            [
                'level' => 'B1',
                'lesson' => 'Present Perfect vs Past Simple',
                'title' => 'Present Perfect vs Past Simple',
                'category' => 'Tenses',
                'theory' => "Оба времени описывают прошлое, но с разным акцентом.\n\n"
                    ."Past Simple: действие завершено в КОНКРЕТНЫЙ момент прошлого, важен САМ факт/момент.\n"
                    ."Пример: I visited Paris in 2019. She called me yesterday.\n\n"
                    ."Present Perfect: момент не важен или неизвестен, важен РЕЗУЛЬТАТ для настоящего.\n"
                    ."Пример: I have visited Paris (когда-то, неважно когда — есть опыт). She has just called me (только что, есть результат — я знаю новость).\n\n"
                    ."Ключевые слова-подсказки:\n"
                    ."Past Simple: yesterday, last year, in 2020, ago, when.\n"
                    ."Present Perfect: already, just, yet, ever, never, since, for, so far.\n\n"
                    ."Частая ошибка: 'I have seen him yesterday' — неправильно, так как yesterday указывает конкретное время → нужно Past Simple: 'I saw him yesterday'.",
            ],
            [
                'level' => 'B1',
                'lesson' => 'Second Conditional',
                'title' => 'Second Conditional (нереальное условие)',
                'category' => 'Sentences',
                'theory' => "Second Conditional используется для гипотетических, нереальных или маловероятных ситуаций в настоящем/будущем.\n\n"
                    ."Формула: If + Past Simple, ... would + глагол.\n"
                    ."Пример: If I had more money, I would travel around the world. If I were you, I would apologize.\n\n"
                    ."С глаголом to be в условной части традиционно используется were для всех лиц (I were, he were), хотя в разговорной речи встречается и was.\n\n"
                    ."Отличие от First Conditional: First — реальная ситуация ('If it rains, I will stay home' — дождь вполне возможен), Second — нереальная/маловероятная ситуация ('If I won the lottery, I would buy a house' — маловероятно).\n\n"
                    ."'If I were you, I would...' — устойчивая фраза для совета.",
            ],
            [
                'level' => 'B1',
                'lesson' => 'Passive Voice: Advanced Structures',
                'title' => 'Страдательный залог (Passive Voice)',
                'category' => 'Verbs and Voices',
                'theory' => "Страдательный залог используется, когда важнее само действие или его результат, а не тот, кто его совершил.\n\n"
                    ."Формула: be (в нужном времени) + глагол в 3-й форме (Participle II).\n\n"
                    ."Present Simple Passive: The letters are sent every day.\n"
                    ."Past Simple Passive: The house was built in 1990.\n"
                    ."Present Perfect Passive: The work has been finished.\n"
                    ."Future Passive: The report will be sent tomorrow.\n\n"
                    ."Если нужно указать, кто совершил действие, используется by: The book was written by Tolstoy.\n\n"
                    ."Активный залог: Someone stole my bike. → Пассивный залог: My bike was stolen.\n"
                    .'Пассив часто используется в новостях, научных текстах, официальных документах, где деятель неважен или неизвестен.',
            ],
            [
                'level' => 'B1',
                'lesson' => 'Reported Speech: Statements',
                'title' => 'Косвенная речь: утверждения',
                'category' => 'Sentences',
                'theory' => "Косвенная речь используется, чтобы передать чужие слова без кавычек. При этом обычно происходит сдвиг времени на один шаг назад.\n\n"
                    ."Present Simple → Past Simple: 'I work here.' → He said (that) he worked there.\n"
                    ."Present Continuous → Past Continuous: 'I am reading.' → She said she was reading.\n"
                    ."Past Simple → Past Perfect: 'I saw him.' → He said he had seen him.\n"
                    ."Present Perfect → Past Perfect: 'I have finished.' → She said she had finished.\n"
                    ."will → would: 'I will help.' → He said he would help.\n\n"
                    ."Также меняются местоимения и указатели места/времени: I → he/she, here → there, today → that day, tomorrow → the next day, yesterday → the day before.\n\n"
                    ."Пример полностью: 'I am tired today,' she said. → She said (that) she was tired that day.",
            ],
            [
                'level' => 'B1',
                'lesson' => 'Relative Clauses',
                'title' => 'Относительные придаточные (Relative Clauses)',
                'category' => 'Phrases and Clauses',
                'theory' => "Relative clauses уточняют, о каком именно человеке, предмете или месте идёт речь, с помощью относительных местоимений.\n\n"
                    ."Who — для людей: The man who called you is my brother.\n"
                    ."Which — для предметов/животных: The book which is on the table is mine.\n"
                    ."That — для людей и предметов (в defining clauses): The film that we watched was great.\n"
                    ."Whose — притяжательное: The woman whose car was stolen called the police.\n"
                    ."Where — для места: This is the house where I grew up.\n\n"
                    ."Defining clauses (без запятых) — необходимая информация, без которой смысл предложения непонятен.\n"
                    ."Non-defining clauses (в запятых) — дополнительная информация, которую можно убрать: My brother, who lives in London, is a doctor.\n\n"
                    .'В defining clauses who/which/that можно опустить, если оно является дополнением: The book (that) I read was interesting.',
            ],

            // B2
            [
                'level' => 'B2',
                'lesson' => 'Third Conditional',
                'title' => 'Third Conditional (нереальное прошлое)',
                'category' => 'Sentences',
                'theory' => "Third Conditional используется для гипотетических ситуаций в ПРОШЛОМ, которые не произошли, и их воображаемых последствий.\n\n"
                    ."Формула: If + Past Perfect, ... would have + глагол в 3-й форме.\n"
                    ."Пример: If I had studied harder, I would have passed the exam. (В реальности я не учился усерднее и не сдал экзамен.)\n\n"
                    ."Отрицательная форма: If I hadn't been late, I wouldn't have missed the train.\n\n"
                    ."Third Conditional часто выражает сожаление о прошлом: If I had known about the meeting, I would have come.\n\n"
                    .'Сравнение всех условных: Zero — общие истины (If you heat ice, it melts), First — реальное будущее, Second — нереальное настоящее/будущее, Third — нереальное прошлое.',
            ],
            [
                'level' => 'B2',
                'lesson' => 'Mixed Conditionals',
                'title' => 'Смешанные условные предложения',
                'category' => 'Sentences',
                'theory' => "Mixed Conditionals объединяют разные времена в одном условном предложении, когда условие относится к одному времени, а результат — к другому.\n\n"
                    ."Тип 1: нереальное прошлое → результат в настоящем.\n"
                    ."Формула: If + Past Perfect, ... would + глагол.\n"
                    ."Пример: If I had studied medicine, I would be a doctor now. (Я не изучал медицину в прошлом, поэтому сейчас я не врач.)\n\n"
                    ."Тип 2: нереальное настоящее (постоянная характеристика) → результат в прошлом.\n"
                    ."Формула: If + Past Simple, ... would have + глагол в 3-й форме.\n"
                    ."Пример: If I weren't so shy, I would have asked her out. (Я вообще стеснительный человек, поэтому в прошлом не пригласил её на свидание.)\n\n"
                    .'Смешанные условные показывают связь между разными временными пластами и часто используются в естественной речи носителей.',
            ],
            [
                'level' => 'B2',
                'lesson' => 'Inversion for Emphasis',
                'title' => 'Инверсия для усиления',
                'category' => 'Grammatical Functions',
                'theory' => "Инверсия — изменение обычного порядка слов (подлежащее + сказуемое → сказуемое + подлежащее) для придания эмоциональной выразительности, часто в формальной или литературной речи.\n\n"
                    ."После отрицательных наречий в начале предложения: Never, Rarely, Seldom, Not only, No sooner, Hardly.\n"
                    ."Пример: Never have I seen such a beautiful sunset. Not only did she win the race, but she also broke the record.\n\n"
                    ."После So + прилагательное и Such + существительное:\n"
                    ."Пример: So surprised was he that he couldn't speak.\n\n"
                    ."После условных предложений без if (формальный стиль):\n"
                    ."Пример: Had I known, I would have come earlier. (= If I had known...)\n\n"
                    .'Инверсия придаёт речи книжный, формальный или драматический оттенок и часто встречается в академических и литературных текстах.',
            ],
            [
                'level' => 'B2',
                'lesson' => 'Subjunctive Mood',
                'title' => 'Сослагательное наклонение (Subjunctive Mood)',
                'category' => 'Moods',
                'theory' => "Subjunctive Mood выражает пожелания, требования, предположения и нереальные ситуации. Используется начальная форма глагола независимо от лица.\n\n"
                    ."После глаголов suggest, recommend, insist, demand, propose + that:\n"
                    ."Пример: I suggest that he study harder. (не 'studies') The doctor recommended that she rest for a week.\n\n"
                    ."После выражений it is important/essential/vital that:\n"
                    ."Пример: It is essential that everyone be on time.\n\n"
                    ."С were вместо was в нереальных условиях и после wish/if only:\n"
                    ."Пример: I wish I were taller. If only he were here now.\n\n"
                    .'Subjunctive чаще встречается в формальном/письменном английском, особенно в американском варианте.',
            ],
            [
                'level' => 'B2',
                'lesson' => 'Reported Speech: Advanced Reporting Verbs',
                'title' => 'Косвенная речь: продвинутые глаголы',
                'category' => 'Verbs and Voices',
                'theory' => "Помимо say и tell, в косвенной речи используется множество более точных глаголов, которые сразу передают тон и намерение говорящего.\n\n"
                    ."Совет: advise, recommend + doing/to do.\n"
                    ."Пример: He advised me to see a doctor.\n\n"
                    ."Приказ/просьба: order, ask, beg + object + to do.\n"
                    ."Пример: The teacher asked us to be quiet.\n\n"
                    ."Отказ/согласие: refuse, agree, offer + to do.\n"
                    ."Пример: She refused to answer the question.\n\n"
                    ."Обвинение/извинение: accuse (of + doing), apologise (for + doing), deny (doing).\n"
                    ."Пример: He denied stealing the money. She apologised for being late.\n\n"
                    .'Использование точного глагола вместо say/tell делает речь более естественной и информативной — сразу понятна интонация и цель высказывания.',
            ],

            // C1
            [
                'level' => 'C1',
                'lesson' => 'Participle Clauses',
                'title' => 'Причастные обороты (Participle Clauses)',
                'category' => 'Phrases and Clauses',
                'theory' => "Participle clauses позволяют сокращать придаточные предложения, делая речь более компактной и характерной для письменного/формального стиля.\n\n"
                    ."Present Participle (-ing) — для одновременных или активных действий:\n"
                    ."Пример: Feeling tired, she went to bed early. (= Because she felt tired...)\n\n"
                    ."Past Participle (-ed/3-я форма) — для пассивного значения:\n"
                    ."Пример: Written in 1997, the novel became a bestseller. (= The novel, which was written in 1997...)\n\n"
                    ."Perfect Participle (having + 3-я форма) — для действия, завершившегося ДО другого действия:\n"
                    ."Пример: Having finished the report, he went home. (= After he had finished the report...)\n\n"
                    .'Participle clauses часто заменяют придаточные причины, времени и определительные придаточные, делая текст лаконичнее — характерная черта академического и художественного стиля.',
            ],
            [
                'level' => 'C1',
                'lesson' => 'Nominalization',
                'title' => 'Номинализация (Nominalization)',
                'category' => 'Etymology and Morphology',
                'theory' => "Номинализация — превращение глагола или прилагательного в существительное. Это ключевая черта формального и академического английского.\n\n"
                    ."Глагол → существительное: decide → decision, arrive → arrival, analyse → analysis, develop → development.\n"
                    ."Прилагательное → существительное: important → importance, difficult → difficulty, aware → awareness.\n\n"
                    ."Разговорный стиль: They decided to change the policy, which improved the results.\n"
                    ."Академический стиль: The decision to change the policy resulted in an improvement in the results.\n\n"
                    ."Номинализация позволяет:\n"
                    ."— уплотнить информацию в одном предложении;\n"
                    ."— убрать субъекта действия, если он неважен (объективный, безличный тон);\n"
                    ."— связывать идеи между предложениями (тема-рематическая связь).\n\n"
                    .'Это одна из главных стилистических техник в эссе, научных статьях и отчётах.',
            ],
            [
                'level' => 'C1',
                'lesson' => 'Fronting for Emphasis',
                'title' => 'Вынесение вперёд (Fronting)',
                'category' => 'Grammatical Functions',
                'theory' => "Fronting — вынесение элемента предложения (не подлежащего) в начало для смыслового акцента. Характерно для риторики, литературы и выразительной речи.\n\n"
                    ."Вынесение дополнения:\n"
                    ."Пример: This I cannot accept. (= I cannot accept this.)\n\n"
                    ."Вынесение обстоятельства места/направления (часто с инверсией):\n"
                    ."Пример: Down the street came a strange procession.\n\n"
                    ."Вынесение прилагательного/причастия:\n"
                    ."Пример: Exhausted but happy, the runners crossed the finish line.\n\n"
                    .'Fronting привлекает внимание к вынесенному элементу и создаёт эффект неожиданности или драматизма — приём, часто используемый в художественной литературе, ораторской речи и заголовках.',
            ],
            [
                'level' => 'C1',
                'lesson' => 'Ellipsis',
                'title' => 'Эллипсис (пропуск слов)',
                'category' => 'Grammatical Functions',
                'theory' => "Эллипсис — намеренный пропуск слов, которые понятны из контекста, чтобы избежать повторов и сделать речь более естественной.\n\n"
                    ."Пропуск подлежащего в разговорной речи: (I) Sounds good! (Are you) Ready?\n\n"
                    ."Пропуск повторяющегося глагола после and/but/or:\n"
                    ."Пример: She likes tea, and he coffee. (= ...and he likes coffee.)\n\n"
                    ."Пропуск после вспомогательных глаголов в кратких ответах:\n"
                    ."Пример: 'Have you finished?' 'Yes, I have (finished).'\n\n"
                    ."Пропуск в сравнительных конструкциях: She earns more than he (does).\n\n"
                    .'Эллипсис делает речь экономнее и естественнее, но требует хорошего понимания контекста — иначе пропущенная часть может быть непонятна собеседнику.',
            ],
            [
                'level' => 'C1',
                'lesson' => 'Hedging Language',
                'title' => 'Хеджирование (смягчение высказывания)',
                'category' => 'Miscellaneous Grammar Subjects',
                'theory' => "Hedging — использование смягчающих конструкций, чтобы высказывание звучало менее категорично, более вежливо или осторожно. Особенно важно в академическом и деловом английском.\n\n"
                    ."Модальные глаголы: may, might, could, would suggest.\n"
                    ."Пример: This might explain the results. The data would suggest a correlation.\n\n"
                    ."Вводные фразы: it seems that, it appears that, it could be argued that, arguably.\n"
                    ."Пример: It appears that the policy has had a limited effect.\n\n"
                    ."Наречия степени: relatively, somewhat, to some extent, largely.\n"
                    ."Пример: The results were somewhat inconclusive.\n\n"
                    .'Hedging позволяет избежать излишне категоричных утверждений (avoid, prevent absolute claims), что особенно важно в научных работах, где выводы часто носят вероятностный характер, а также в деловой переписке для сохранения вежливого тона.',
            ],

            // Новые темы — категории, которых раньше не было в списке
            [
                'level' => 'A1',
                'lesson' => 'Personal Pronouns',
                'title' => 'Местоимения (Pronouns)',
                'category' => 'Pronouns',
                'theory' => "Местоимения (pronouns) заменяют существительные, чтобы избежать повторов и сделать речь короче и естественнее.\n\nКогда используется:\n— вместо имени человека или названия предмета, о котором уже шла речь: Tom is my friend. He lives near me.\n— чтобы не повторять одно и то же существительное несколько раз подряд.\n— в безличных конструкциях о погоде, времени, расстоянии: It's raining. It's 5 o'clock.\n\nФормы (основные группы):\nЛичные (I, you, he...) — подробнее в теме Personal Pronouns. Объектные (me, him...) — Object Pronouns. Притяжательные (mine, his...) — Possessive Pronouns. Возвратные (myself...) — Reflexive Pronouns. Указательные (this, that...) — Demonstrative Pronouns. Вопросительные (who, what...) — Interrogative Pronouns. Относительные (who, which...) — Relative Pronouns. Неопределённые (someone, anything...) — Indefinite Pronouns.\n\nПримеры:\nShe works in a bank. I gave the book to her. This is my bag; that one is yours. Who called you? The man who called you is my uncle. Somebody left this here.\n\nДополнительные примеры:\nThey live in London. We saw them at the concert. Is this pen yours? I hurt myself. Everyone knows the answer. Each of them has a key.\n\nЧастые ошибки:\n— Смешение подлежащего и дополнения: 'Me and him went to the party' — неправильно, нужно 'He and I went to the party'.\n— Отсутствие согласования числа: 'Everyone have finished' — неправильно, нужно 'Everyone has finished' (everyone — единственное число).",
            ],
            [
                'level' => 'A1',
                'lesson' => '',
                'title' => 'Существительные (Nouns)',
                'category' => 'Nouns',
                'theory' => "Существительные (nouns) называют людей, места, предметы, вещества, качества, действия или понятия — практически всё, о чём можно говорить.\n\nКогда используется:\n— чтобы назвать предмет или человека, о котором идёт речь: The teacher explained the lesson.\n— как подлежащее, дополнение или после предлога в предложении.\n— чтобы отнести объект к одной из категорий: собственное или нарицательное, исчисляемое или неисчисляемое, конкретное или абстрактное (см. отдельные темы).\n\nФормы (основные категории):\nСобственные/нарицательные (Proper and Common), исчисляемые/неисчисляемые (Countable and Uncountable), единственное/множественное число (Singular and Plural), собирательные (Collective), абстрактные/конкретные (Abstract and Concrete), составные (Compound Nouns).\n\nПримеры:\ntable, teacher, London, happiness, water, team, information, sunshine.\n\nДополнительные примеры:\nThe children are playing in the park. Honesty is an important quality. My brother works for a large company. We need more information about the project.\n\nЧастые ошибки:\n— Использование неисчисляемых существительных с a/an или во множественном числе: 'an informations' — неправильно, information неисчисляемо: 'some information'.\n— Отсутствие заглавной буквы у собственных существительных: 'i live in london' — неправильно, нужно 'I live in London'.",
            ],
            [
                'level' => 'A2',
                'lesson' => '',
                'title' => 'Наречия (Adverbs)',
                'category' => 'Adverbs',
                'theory' => "Наречия (adverbs) описывают, как, когда, где или как часто происходит действие, и обычно относятся к глаголу, прилагательному или другому наречию.\n\nКогда используется:\n— чтобы описать образ действия: She sings beautifully.\n— чтобы указать время, место или частоту действия: yesterday, here, often.\n— чтобы усилить или ослабить значение прилагательного или другого наречия: very difficult, quite fluently.\n— чтобы связать предложения или выразить отношение говорящего к сказанному: however, fortunately.\n\nФормы (основные типы наречий):\nManner (образа действия), Place (места), Time (времени), Frequency (частоты), Degree (степени), Probability (вероятности) — подробнее в отдельных темах этой категории.\n\nПримеры:\nShe drives carefully. We arrived early. I looked everywhere. He rarely complains. This is extremely important. Surprisingly, she agreed.\n\nДополнительные примеры:\nHe speaks English fluently. They left yesterday. I've never been there. It's quite cold today. Honestly, I don't know.\n\nЧастые ошибки:\n— Наречие вместо прилагательного после глагола-связки: 'She looks beautifully' — неправильно, нужно 'She looks beautiful' (look здесь — связка, требует прилагательного).\n— Неверное место наречия в предложении — см. отдельную тему Adverb Placement and Order.",
            ],
            [
                'level' => 'A1',
                'lesson' => "Can and Can't",
                'title' => 'Модальные и полумодальные глаголы (Modals and Semi-modals)',
                'category' => 'Modals and Semi-modals',
                'theory' => "Модальные глаголы (modal verbs) выражают возможность, необходимость, разрешение или совет. После них глагол всегда стоит в начальной форме без to, и они не меняются по лицам.\n\n"
                    ."Can/could — возможность, умение, просьба: I can swim. Could you help me, please?\n\n"
                    ."Must — сильная необходимость (часто исходящая от говорящего): You must wear a seatbelt. Mustn't выражает запрет: You mustn't smoke here.\n\n"
                    ."Should/ought to — совет, рекомендация: You should see a doctor.\n\n"
                    ."May/might — вероятность, формальное разрешение: It may rain later. May I come in?\n\n"
                    ."Полумодальные глаголы (semi-modals) ведут себя как модальные, но изменяются по лицам и временам: have to, need to, used to. Have to выражает необходимость, часто исходящую извне: I have to work on Saturdays.\n\n"
                    ."Важно: don't have to означает отсутствие необходимости (не путать с mustn't — запретом): You don't have to come if you're busy (это необязательно, но можно).",
            ],
            [
                'level' => 'A1',
                'lesson' => 'Prepositions of Place',
                'title' => 'Предлоги и союзы (Prepositions and Conjunctions)',
                'category' => 'Prepositions and Conjunctions',
                'theory' => "Предлоги (prepositions) показывают отношения между словами — место, время, направление. Союзы (conjunctions) соединяют слова, части предложения или целые предложения.\n\n"
                    ."Предлоги места: in (внутри, в городе/стране), on (на поверхности), at (в точке, конкретном месте).\n"
                    ."Примеры: in the box, in London, on the table, on the wall, at the door, at school.\n\n"
                    ."Предлоги времени: in (месяцы, годы, время суток), on (дни, даты), at (точное время).\n"
                    ."Примеры: in June, in 2020, in the evening, on Monday, on my birthday, at 5 o'clock, at night.\n\n"
                    ."Предлоги направления: to, into, from, out of, towards.\n"
                    ."Пример: She walked into the room. He came from Spain.\n\n"
                    ."Сочинительные союзы (and, but, or, so, yet) соединяют равноправные части: I like tea and coffee. She was tired, but she kept working.\n\n"
                    ."Подчинительные союзы (because, although, if, when, since) вводят придаточные предложения: I stayed home because it was raining. Although he was tired, he finished the work.",
            ],
            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Пунктуация и орфография (Punctuation and Spelling)',
                'category' => 'Punctuation and Spelling',
                'theory' => "Пунктуация и орфография — важная часть письменной грамотности в английском языке.\n\n"
                    ."Запятая (comma) разделяет элементы списка, отделяет вводные слова и части сложного предложения: I bought apples, bananas, and oranges. However, she decided to stay.\n\n"
                    ."Апостроф (apostrophe) используется в сокращениях (don't, it's, she's) и для притяжательного падежа: the student's book (один студент), the students' books (несколько студентов).\n\n"
                    ."Заглавная буква ставится в начале предложения, у имён собственных, названий дней недели, месяцев и национальностей: Monday, June, English, London.\n\n"
                    ."Разница между британским и американским написанием: colour/color, favourite/favorite, centre/center, travelling/traveling, organise/organize.\n\n"
                    ."Частая ошибка: it's (сокращение от it is/it has) vs its (притяжательное местоимение). It's raining. The dog wagged its tail.",
            ],

            // Подтемы категории Pronouns
            [
                'level' => 'A1',
                'lesson' => '',
                'title' => 'Personal Pronouns',
                'category' => 'Pronouns',
                'theory' => "Personal pronouns (личные местоимения) — полная система форм для каждого лица и числа, заменяющая имена людей и предметов в роли подлежащего или дополнения.\n\nКогда используется:\n— чтобы назвать говорящего, слушающего или того, о ком идёт речь, не повторяя имя: Maria is tired. She wants to sleep.\n— it для предметов, животных (пол неизвестен) и в безличных конструкциях: It's cold today.\n— they как местоимение единственного числа, когда пол человека неизвестен или не указывается: Someone left their umbrella here.\n\nФормы (подлежащее / дополнение):\nI / me — you / you — he / him — she / her — it / it — we / us — they / them.\n\nПримеры:\nI live in Tashkent. You are my best friend. He works at a hospital. She speaks three languages. We study together. They live next door. It's on the table.\n\nДополнительные примеры:\nCan you help me? I gave the book to her. Did you see him at the party? We invited them to dinner. It doesn't matter. Someone left their phone here.\n\nЧастые ошибки:\n— Путаница форм: 'Him is my brother' — неправильно, нужно 'He is my brother' (подлежащее — he, не him).\n— he/she вместо it для предметов: 'Where is the book? He is on the table' — неправильно, нужно 'It is on the table'.",
            ],
            [
                'level' => 'A1',
                'lesson' => '',
                'title' => 'Subject Pronouns',
                'category' => 'Pronouns',
                'theory' => "Subject pronouns (местоимения-подлежащие) — I, you, he, she, it, we, they — стоят перед глаголом и показывают, кто выполняет действие.\n\nКогда используется:\n— в роли подлежащего перед смысловым глаголом или глаголом to be: She works at a hospital.\n— после сравнительных союзов than/as в формальном стиле: He is taller than I (am).\n— в кратких ответах в формальном стиле: 'Who did it?' — 'I did.'\n\nФормы:\nI, you, he, she, it, we, they.\n\nПримеры:\nI live in Tashkent. You are very kind. He works at a hospital. She studies English every day. It is raining. We play football on Sundays. They travel a lot.\n\nДополнительные примеры:\nHe and I went to the cinema. She is younger than I am. Are you coming with us? It doesn't work anymore. We are all excited about the trip.\n\nЧастые ошибки:\n— Использование объектной формы вместо подлежащего: 'Me and him went to the party' — неправильно, нужно 'He and I went to the party'.\n— Порядок вежливости: невежливо ставить 'I' первым в перечислении: 'I and my friend' лучше заменить на 'My friend and I'.",
            ],
            [
                'level' => 'A1',
                'lesson' => '',
                'title' => 'Object Pronouns',
                'category' => 'Pronouns',
                'theory' => "Object pronouns (объектные местоимения) — me, you, him, her, it, us, them — используются как прямое или косвенное дополнение, отвечая на вопрос «кого? что? кому?».\n\nКогда используется:\n— как прямое дополнение после глагола: I saw her yesterday.\n— как косвенное дополнение перед прямым: She gave him a present.\n— после предлогов: This letter is for you. Come with us.\n— в неформальных кратких ответах: 'Who wants coffee?' — 'Me!'\n\nФормы:\nme, you, him, her, it, us, them.\n\nПримеры:\nCall me later. I saw him at the shop. She gave her the keys. Please help us. Can you see them? This gift is for you. Give it to me.\n\nДополнительные примеры:\nHe told us the news. I met her at the airport. Did you invite them? Between you and me, I don't trust him. Wait for me!\n\nЧастые ошибки:\n— Использование подлежащего вместо дополнения после предлога: 'This is between you and I' — неправильно, нужно 'This is between you and me'.\n— Использование объектной формы как подлежащего: 'Him works here' — неправильно, нужно 'He works here'.",
            ],
            [
                'level' => 'A2',
                'lesson' => '',
                'title' => 'Reflexive Pronouns',
                'category' => 'Pronouns',
                'theory' => "Reflexive pronouns (возвратные местоимения) — myself, yourself, himself, herself, itself, oneself, ourselves, yourselves, themselves — используются, когда подлежащее и дополнение обозначают одно и то же лицо.\n\nКогда используется:\n— подлежащее и объект действия совпадают: I hurt myself.\n— в устойчивых выражениях: enjoy yourself, help yourself, behave yourself.\n— by + reflexive = «в одиночку, самостоятельно»: I did it by myself.\n— НЕ используется как эмфатическое усиление — для этого те же формы играют другую роль (см. тему Emphatic Pronouns).\n\nФормы:\nmyself, yourself, himself, herself, itself, oneself, ourselves, yourselves, themselves.\n\nПримеры:\nI taught myself to play the guitar. She looked at herself in the mirror. Be careful — don't cut yourself! They introduced themselves to the group. Enjoy yourselves at the party! He lives by himself.\n\nДополнительные примеры:\nWe should ask ourselves why this happened. The cat was washing itself. Help yourself to some cake. I can't believe I did that myself. Did you hurt yourself?\n\nЧастые ошибки:\n— Reflexive там, где в английском оно не нужно: 'I washed myself' звучит нормально только про гигиену целиком; для 'I feel good' не нужно 'I feel myself good' — это ошибка, просто 'I feel good'.\n— Путаница с emphatic pronouns: 'I myself did it' (усиление, можно убрать) отличается от 'I did it myself' в значении «сам, без чужой помощи» — обе формы правильны, но разная роль.",
            ],
            [
                'level' => 'A2',
                'lesson' => '',
                'title' => 'Demonstrative Pronouns',
                'category' => 'Pronouns',
                'theory' => "Demonstrative pronouns (указательные местоимения) — this, that, these, those — указывают на конкретный предмет или предметы в пространстве или времени.\n\nКогда используется:\n— this/these для близких по расстоянию или времени предметов: This is my bag.\n— that/those для далёких предметов: That was a great film.\n— чтобы сослаться на уже упомянутую идею или ситуацию: That's a good idea!\n— в начале телефонного разговора (this — кто говорит, that — кто слушает, в британском варианте): This is Anna. Who is that?\n\nФормы:\nthis (ед. ч., близко), these (мн. ч., близко), that (ед. ч., далеко), those (мн. ч., далеко).\n\nПримеры:\nThis is my new phone. These are my parents. That was an amazing trip. Those shoes look expensive. Is this yours? I don't like that.\n\nДополнительные примеры:\nThese are the best cookies I've ever had. That's exactly what I meant. Can you pass me those, please? This is harder than I expected. Who are those people?\n\nЧастые ошибки:\n— Несогласование числа: 'This are my keys' — неправильно, нужно 'These are my keys'.\n— Путаница this/that по контексту эмоций: that часто используется для критики или дистанцирования ('What is that supposed to mean?'), а this — для чего-то более близкого/актуального говорящему.",
            ],
            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Emphatic Pronouns',
                'category' => 'Pronouns',
                'theory' => "Emphatic pronouns (эмфатические местоимения) используют те же формы, что и reflexive (myself, himself...), но не являются дополнением — они лишь усиливают подлежащее или дополнение, подчёркивая «сам, лично».\n\nКогда используется:\n— чтобы подчеркнуть, что действие сделал именно этот человек, а не кто-то другой: I myself don't believe it.\n— в конце предложения для усиления смысла «без посторонней помощи»: She repaired the car herself.\n— чтобы выделить важность или статус лица: The director himself called me.\n\nФормы:\nТе же, что у reflexive pronouns: myself, yourself, himself, herself, itself, ourselves, yourselves, themselves — но роль в предложении другая, эмфатическое местоимение можно убрать без потери грамматической правильности.\n\nПримеры:\nI myself saw the accident. The manager himself apologised. She did all the work herself. We built the house ourselves. Did you write this yourself? The president himself signed the letter.\n\nДополнительные примеры:\nI'll deal with it myself. They themselves admitted the mistake. He himself doesn't know the answer. The children made the cake themselves. You said it yourself!\n\nЧастые ошибки:\n— Путаница с reflexive: в 'She hurt herself' местоимение обязательно (reflexive, дополнение); в 'She herself opened the door' его можно убрать (emphatic, усиление) — 'She opened the door' по-прежнему грамматически верно.\n— Использование emphatic вместо простого личного местоимения без нужды в усилении: не стоит добавлять myself/himself, если акцент на «сам» не нужен по смыслу.",
            ],
            [
                'level' => 'A2',
                'lesson' => '',
                'title' => 'Interrogative Pronouns',
                'category' => 'Pronouns',
                'theory' => "Interrogative pronouns (вопросительные местоимения) — who, whom, whose, what, which — используются для формирования вопросов о людях, предметах и принадлежности.\n\nКогда используется:\n— who для вопроса о подлежащем-человеке: Who called you?\n— whom для вопроса о дополнении-человеке (формальный стиль, в разговорной речи обычно заменяется на who): Whom did you meet?\n— whose для вопроса о принадлежности: Whose bag is this?\n— what для открытого вопроса без ограничения вариантов: What do you want?\n— which для вопроса с ограниченным выбором: Which do you prefer, tea or coffee?\n\nФормы:\nwho (подлежащее), whom (дополнение, формально), whose (принадлежность), what (открытый выбор), which (ограниченный выбор).\n\nПримеры:\nWho is that man? Whom did you invite? Whose car is parked outside? What happened yesterday? Which colour do you like better, blue or green? What time is it?\n\nДополнительные примеры:\nWho taught you English? Whose idea was this? What are you thinking about? Which of these books is yours? To whom should I address the letter?\n\nЧастые ошибки:\n— what вместо which при ограниченном выборе: 'What do you prefer, tea or coffee?' звучит менее естественно, чем 'Which do you prefer, tea or coffee?'.\n— who вместо whose для принадлежности: 'Who is this bag?' — неправильно, нужно 'Whose is this bag?' или 'Whose bag is this?'.",
            ],
            [
                'level' => 'A2',
                'lesson' => '',
                'title' => 'Possessive Pronouns',
                'category' => 'Pronouns',
                'theory' => "Possessive pronouns (притяжательные местоимения) — mine, yours, his, hers, its, ours, theirs — заменяют «притяжательное прилагательное + существительное» и употребляются самостоятельно, без существительного после них.\n\nКогда используется:\n— чтобы не повторять уже упомянутое существительное: This is my book. → This book is mine.\n— после предлога of, чтобы выразить принадлежность: a friend of mine.\n— в кратких ответах о принадлежности: 'Whose is this?' — 'Mine.'\n\nФормы:\nmine, yours, his, hers, its (редко используется самостоятельно), ours, theirs.\n\nПримеры:\nIs this pen yours? This house is ours. That mistake was mine. The red car is his. These seats are theirs. A colleague of mine recommended this book.\n\nДополнительные примеры:\nWhose idea was this? — It was hers. Our team played better than theirs. Is that bag yours or mine? The choice is yours. That opinion is entirely his own.\n\nЧастые ошибки:\n— Путаница с possessive adjectives (my, your, his, her, its, our, their), которые всегда стоят перед существительным: 'This book is my' — неправильно, нужно 'This book is mine' (без последующего существительного — pronoun).\n— Апостроф у its: possessive pronoun its пишется без апострофа; it's — это сокращение от it is/it has.",
            ],
            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Indefinite Pronouns',
                'category' => 'Pronouns',
                'theory' => "Indefinite pronouns (неопределённые местоимения) обозначают неопределённых или неуказанных людей, предметы и количества.\n\nКогда используется:\n— someone/somebody, something — в утвердительных предложениях: I saw someone in the garden.\n— anyone/anybody, anything — в вопросах и отрицаниях: Did you see anyone? I didn't see anything.\n— no one/nobody, nothing — уже содержат отрицание: Nobody knows the answer.\n— everyone/everybody, everything — обо всех/всём без исключения: Everyone enjoyed the party.\n— all, both, none, several, few, many — для количества среди исчисляемых существительных.\n\nФормы:\n-body/-one/-thing группы: some-, any-, no-, every-. Плюс: all, both, none, several, few, many, each.\n\nПримеры:\nSomeone is knocking at the door. I don't have anything to wear. Nobody called while you were out. Everybody knows the rules. Few people understood the lecture. Both of them agreed.\n\nДополнительные примеры:\nIs there anyone home? I need something to drink. None of the answers were correct. Everything is ready for the party. Several students missed the class. Many of us disagreed.\n\nЧастые ошибки:\n— Двойное отрицание: 'Nobody doesn't know' — неправильно, нужно 'Nobody knows' (no- уже отрицание, глагол ставится в утвердительной форме).\n— Несогласование числа: 'Everybody are here' — неправильно, нужно 'Everybody is here' (местоимения на -body/-one/-thing — единственное число).",
            ],
            [
                'level' => 'B2',
                'lesson' => '',
                'title' => 'Dummy Pronouns',
                'category' => 'Pronouns',
                'theory' => "Dummy pronouns (местоимения-«пустышки») — it и there — заполняют обязательную позицию подлежащего в английском предложении, не указывая на конкретный предмет.\n\nКогда используется:\n— it для погоды: It's raining. It's sunny today.\n— it для времени и даты: It's 9 o'clock. It's Monday.\n— it для расстояния: It's 200 km to the city.\n— it для общих оценок с прилагательным + to-infinitive: It's important to be honest.\n— there для сообщения о существовании чего-либо (there is/are): There is a café on the corner.\n\nФормы:\nit (безличное) и there (бытийное, существование) — оба грамматически обязательны как подлежащее, даже без реального значения.\n\nПримеры:\nIt's cold outside. It's difficult to say. There is a problem with the printer. There are three chairs in the room. It's a shame you couldn't come. There used to be a shop here.\n\nДополнительные примеры:\nIt's getting late. There isn't much time left. It's worth trying. There will be a meeting tomorrow. It seems that nobody is home.\n\nЧастые ошибки:\n— Пропуск подлежащего (по аналогии с языками, где оно опускается): 'Is raining' — неправильно, нужно 'It's raining'.\n— Путаница it/there: 'It is a book on the table' — неправильно (речь о существовании предмета), нужно 'There is a book on the table'.",
            ],
            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Reciprocal Pronouns',
                'category' => 'Pronouns',
                'theory' => "Reciprocal pronouns (взаимные местоимения) — each other и one another — показывают, что действие взаимно направлено между двумя и более участниками.\n\nКогда используется:\n— когда два и более человека выполняют одинаковое действие по отношению друг к другу: They love each other.\n— в притяжательной форме each other's / one another's: They celebrated each other's birthdays.\n— традиционно each other — для двух, one another — для троих и более, хотя в современном языке различие практически стёрлось.\n\nФормы:\neach other, one another (+ притяжательные each other's, one another's).\n\nПримеры:\nTom and Jerry always argue with each other. The students helped one another during the exam. We've known each other for ten years. They borrowed each other's books. The teammates support one another.\n\nДополнительные примеры:\nDo you and your sister call each other often? The neighbours look after one another's pets. They gave each other presents. The two companies compete with one another. We should listen to each other more.\n\nЧастые ошибки:\n— Путаница с reflexive pronouns: 'They blamed themselves' (каждый винил себя самого) означает не то же самое, что 'They blamed each other' (они винили друг друга).\n— Пропуск апострофа в притяжательной форме: 'each others books' — неправильно, нужно 'each other's books'.",
            ],
            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Relative Pronouns',
                'category' => 'Pronouns',
                'theory' => "Relative pronouns (относительные местоимения) — who, whom, whose, which, that — вводят придаточное предложение и одновременно заменяют существительное, о котором идёт речь.\n\nКогда используется:\n— who для людей в роли подлежащего придаточного: The woman who called you is my aunt.\n— whom для людей в роли дополнения (формально): The man whom I met was very kind.\n— which для предметов и животных: The car which I bought is red.\n— that для людей и предметов в defining (уточняющих) придаточных, разговорный вариант: The book that I read was boring.\n— whose для притяжательного значения: The boy whose bike was stolen called the police.\n\nФормы:\nwho (люди, подлежащее), whom (люди, дополнение), whose (принадлежность), which (предметы/животные), that (люди и предметы, defining clauses).\n\nПримеры:\nThe teacher who taught us maths retired last year. This is the house which I grew up in. The girl whose phone rang left the room. That's the film that everyone is talking about. Is he the man whom you mentioned?\n\nДополнительные примеры:\nI have a friend who lives in Canada. The company whose products we sell is very reliable. This is the restaurant that I told you about. The book which won the prize is excellent. She's the woman who saved the child.\n\nЧастые ошибки:\n— which вместо who для людей: 'The man which called you' — неправильно, нужно 'The man who called you'.\n— Лишнее местоимение после relative pronoun: 'The book that I read it was boring' — неправильно, нужно 'The book that I read was boring' (it лишнее).",
            ],
            [
                'level' => 'B2',
                'lesson' => '',
                'title' => 'Impersonal Pronouns',
                'category' => 'Pronouns',
                'theory' => "Impersonal pronouns (безличные местоимения в обобщающем значении) — one, generic you, generic they — используются, чтобы говорить о людях в целом, а не о конкретном человеке.\n\nКогда используется:\n— one в формальном, книжном стиле: One should always tell the truth.\n— generic you в неформальной речи как аналог one: You can't please everyone.\n— generic they для неопределённой группы людей, часто в значении «говорят, что»: They say it's going to rain.\n— they также при упоминании общепринятых правил или обычаев страны/организации: In Japan, they drive on the left.\n\nФормы:\none (формально), you (неформально, обобщённо), they (обобщённо, о «людях вообще» или неизвестном авторе информации).\n\nПримеры:\nOne must follow the rules. You never know what will happen. They say the new restaurant is excellent. One can easily get lost in this city. You have to be patient with children. They believe it will rain tomorrow.\n\nДополнительные примеры:\nOne should always be polite to strangers. You can't always get what you want. They say practice makes perfect. In this country, they celebrate New Year twice. One never forgets one's first job.\n\nЧастые ошибки:\n— Смешение one и he/she в одном предложении: 'One should always trust his instincts' формально требует 'One should always trust one's instincts'.\n— Излишне частое использование one в неформальной речи — звучит слишком официально; в разговорном стиле естественнее generic you.",
            ],
            [
                'level' => 'C1',
                'lesson' => '',
                'title' => 'Nominal Relative Pronouns',
                'category' => 'Pronouns',
                'theory' => "Nominal relative pronouns (номинативные относительные местоимения) — what, whoever, whatever, whichever — одновременно выполняют роль относительного местоимения и его антецедента, образуя целое именное придаточное предложение.\n\nКогда используется:\n— what в значении «то, что»: What she said surprised everyone. (= The thing that she said)\n— whoever в значении «тот, кто» / «любой, кто»: Whoever wins the race gets a prize.\n— whatever в значении «что угодно, что»: You can do whatever you like.\n— whichever выбирает из ограниченного набора вариантов: Choose whichever option suits you best.\n\nФормы:\nwhat, whoever, whatever, whichever, whomever (реже). Не требуют отдельного существительного перед собой — сами являются подлежащим или дополнением всего предложения.\n\nПримеры:\nWhat you need is a good rest. I don't understand what you mean. Whoever finishes first wins. Take whatever you want from the fridge. Whichever team wins, I'll be happy. What surprised me most was his reaction.\n\nДополнительные примеры:\nWhoever broke the window should apologise. I'll do whatever it takes to succeed. What matters most is your effort. Choose whichever path feels right to you. Whatever happens, we'll be ready.\n\nЧастые ошибки:\n— Использование обычного relative pronoun (which/that) вместо nominal relative, когда антецедента нет: 'I don't know that you mean' — неправильно, нужно 'I don't know what you mean'.\n— Добавление лишнего существительного перед what/whoever: 'The thing what you said' лучше просто 'What you said' — what уже включает значение 'the thing that'.",
            ],
            [
                'level' => 'C1',
                'lesson' => '',
                'title' => 'Archaic Pronouns',
                'category' => 'Pronouns',
                'theory' => "Archaic pronouns (архаичные местоимения) — thou, thee, thy, thine, ye — исторические формы 2-го лица, вышедшие из повседневного употребления к XVIII веку, но встречающиеся в поэзии, Шекспире, Библии короля Якова и стилизованных текстах.\n\nКогда используется:\n— в цитатах из классической литературы и религиозных текстов: Thou art welcome.\n— в поэзии и стилизации под старину для создания торжественного или архаичного тона.\n— в некоторых устойчивых архаизмах, сохранившихся в современном языке: the powers that be.\n— НЕ используется в современной повседневной речи — только для понимания старых текстов.\n\nФормы:\nthou (подлежащее, = you), thee (дополнение, = you), thy/thine (притяжательное, = your/yours; thine перед гласным звуком), ye (подлежащее мн. ч., = you).\n\nПримеры:\nThou art welcome. (= You are welcome.) I give thee my word. (= I give you my word.) Thy kingdom come. (= Your kingdom come.) Thine eyes are beautiful. (= Your eyes are beautiful.) Hear ye, hear ye! (= Listen, everyone!)\n\nДополнительные примеры:\nWhither goest thou? (= Where are you going?) I love thee more than words can say. Blessed art thou among women. Ye shall know the truth. Thy will be done.\n\nЧастые ошибки:\n— Использование архаичных форм в современной речи — звучит крайне неестественно и странно вне литературного или религиозного контекста.\n— Путаница thou/thee: thou — подлежащее (как he), thee — дополнение (как him); 'Thee art welcome' — неправильно даже по архаичным нормам, нужно 'Thou art welcome'.",
            ],
            [
                'level' => 'B2',
                'lesson' => '',
                'title' => 'Distributive Pronouns',
                'category' => 'Pronouns',
                'theory' => "Distributive pronouns (дистрибутивные местоимения) — each, either, neither — указывают на членов группы по отдельности, а не на группу как целое.\n\nКогда используется:\n— each — «каждый» из группы (два и более), рассматриваемых индивидуально: Each of the students has a laptop.\n— either — «один из двух» (утвердительно или в вопросе): You can take either of the two roads.\n— neither — «ни один из двух» (отрицательное значение): Neither of the answers is correct.\n— either/neither также используются как ответ-согласие в отрицательных предложениях: 'I don't like it.' — 'Neither do I.'\n\nФормы:\neach, either, neither (+ each of / either of / neither of + существительное во множественном числе).\n\nПримеры:\nEach of them has a key. Either of these options works for me. Neither of the answers is correct. Each costs ten dollars. You can sit on either side. Neither of us knew the answer.\n\nДополнительные примеры:\nEach student received a certificate. I don't mind either restaurant. Neither of the two candidates impressed me. Either way, we'll be late. Each of the rooms has its own bathroom.\n\nЧастые ошибки:\n— Несогласование глагола: 'Each of them have a key' — неправильно, нужно 'Each of them has a key' (each/either/neither — единственное число, даже с 'of + мн. число').\n— Neither с двойным отрицанием: 'I don't like neither of them' — неправильно, нужно 'I like neither of them' или 'I don't like either of them'.",
            ],

            // Подтемы категории Tenses — все 12 времён + обзорные темы
            [
                'level' => 'A2',
                'lesson' => '',
                'title' => 'Tenses',
                'category' => 'Tenses',
                'theory' => "В английском языке 12 основных времён, образованных сочетанием трёх времён (present, past, future) и четырёх аспектов (simple, continuous, perfect, perfect continuous).\n\n"
                    ."Present: Present Simple (I work), Present Continuous (I am working), Present Perfect (I have worked), Present Perfect Continuous (I have been working).\n\n"
                    ."Past: Past Simple (I worked), Past Continuous (I was working), Past Perfect (I had worked), Past Perfect Continuous (I had been working).\n\n"
                    ."Future: Future Simple (I will work), Future Continuous (I will be working), Future Perfect (I will have worked), Future Perfect Continuous (I will have been working).\n\n"
                    ."Ключ к выбору правильного времени — понять две вещи: когда происходит действие (время) и как оно происходит — как разовое действие, процесс, результат или длительный процесс до момента (аспект). Подробнее об этом — в теме Aspects.",
            ],
            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Present Perfect Continuous',
                'category' => 'Tenses',
                'theory' => "Present Perfect Continuous подчёркивает длительность процесса, который начался в прошлом и либо продолжается сейчас, либо только что завершился, оставив видимый результат.\n\nКогда используется:\n— действие, длящееся с определённого момента до сих пор, с акцентом на процесс: I have been learning English for five years.\n— недавно закончившееся действие, следы которого видны сейчас: Why are you wet? — I've been washing the car.\n— временное, повторяющееся действие в недавнем прошлом: She has been calling me all day.\n— выражение раздражения или удивления продолжительностью процесса: I've been waiting for an hour!\n\nСтруктура — утверждение:\nПодлежащее + have/has + been + глагол-ing.\nI have been working. She has been studying all morning.\n\nСтруктура — отрицание:\nПодлежащее + have/has + not + been + глагол-ing.\nI haven't been sleeping well lately. He hasn't been feeling well.\n\nСтруктура — вопрос:\nHave/Has + подлежащее + been + глагол-ing?\nHow long have you been waiting? Has she been crying?\n\nПримеры:\nI've been working on this essay since 9 a.m. It has been raining all day. How long have you been learning Spanish? She's been feeling tired recently. We haven't been sleeping much because of the baby. Have you been exercising? You look great!\n\nДополнительные примеры:\nThey've been renovating the house for months. My eyes hurt — I've been reading for hours. Has he been avoiding you lately? I haven't been eating much junk food this month. The children have been playing outside since lunch. We've been discussing this problem for weeks.\n\nЧастые ошибки:\n— С глаголами состояния: 'I have been knowing him for years' — неправильно, нужно 'I have known him for years' (know — не используется в Continuous формах).\n— Путаница с Present Perfect Simple, когда важно количество, а не длительность: 'I have been writing three emails' лучше заменить на 'I have written three emails' (результат/количество — Simple).\n— Пропуск been: 'I have working all day' — неправильно, нужно 'I have been working all day'.",
            ],
            [
                'level' => 'B1',
                'lesson' => 'Past Perfect Tense',
                'title' => 'Past Perfect',
                'category' => 'Tenses',
                'theory' => "Past Perfect показывает, что одно действие в прошлом произошло раньше другого — «прошлое до прошлого».\n\nКогда используется:\n— действие, завершившееся до другого момента или события в прошлом: When I arrived, the meeting had already started.\n— причина более позднего события в прошлом: She was upset because she had failed the exam.\n— в косвенной речи как сдвинутая форма Past Simple/Present Perfect: He said he had already eaten.\n— нереализованное условие или сожаление о прошлом (в связке с Third Conditional): If I had known, I would have come.\n\nСтруктура — утверждение:\nПодлежащее + had + глагол в 3-й форме.\nI had finished. She had already left. They had never met before.\n\nСтруктура — отрицание:\nПодлежащее + had not (hadn't) + глагол в 3-й форме.\nI hadn't seen that film before. We hadn't heard the news.\n\nСтруктура — вопрос:\nHad + подлежащее + глагол в 3-й форме?\nHad you ever visited Paris before that trip? What had happened before you arrived?\n\nПримеры:\nBy the time we got to the cinema, the film had already started. She had never flown before that trip. I realised I had forgotten my passport. They had finished dinner before we arrived. Had you met him before the interview? He was tired because he had been working all night. (Note: комбинация с Continuous — см. отдельную тему.)\n\nДополнительные примеры:\nWe had lived in three different countries before we settled here. I hadn't expected such a big crowd. Had she ever spoken in public before that presentation? The train had already left when we reached the platform. He had finished his degree before he turned twenty. I wish I had studied harder for that exam.\n\nЧастые ошибки:\n— Past Perfect без сравнения с другим моментом: 'I had visited London last year' — обычно неверно; если событие одно, достаточно Past Simple: 'I visited London last year'.\n— Путаница порядка событий: важно, чтобы Past Perfect описывал более РАННЕЕ событие, а Past Simple — более позднее: 'After she had finished her homework, she went out' (сначала закончила, потом вышла).\n— Использование had вместо has/have в настоящем контексте: 'I had never been there' в контексте, который относится к настоящему, должно быть 'I have never been there'.",
            ],
            [
                'level' => 'B2',
                'lesson' => '',
                'title' => 'Past Perfect Continuous',
                'category' => 'Tenses',
                'theory' => "Past Perfect Continuous показывает процесс, который длился некоторое время ДО определённого момента или события в прошлом — сочетание завершённости к моменту и длительности.\n\nКогда используется:\n— длительное действие, приведшее к результату/ситуации в прошлом: She was exhausted because she had been running.\n— ответ на вопрос «как долго» до момента в прошлом: They had been dating for two years before they got married.\n— причина видимого следствия в прошлом: His clothes were dirty — he had been working in the garden.\n\nСтруктура — утверждение:\nПодлежащее + had + been + глагол-ing.\nI had been waiting for an hour. She had been studying all night.\n\nСтруктура — отрицание:\nПодлежащее + had not (hadn't) + been + глагол-ing.\nWe hadn't been expecting so many guests.\n\nСтруктура — вопрос:\nHad + подлежащее + been + глагол-ing?\nHow long had you been living there before you moved?\n\nПримеры:\nI was out of breath because I had been running. She had been working at the company for ten years before she retired. They had been arguing for a while before we walked in. Had he been drinking before the accident? We had been waiting for two hours when the doctor finally called us in. My eyes were red — I had been crying.\n\nДополнительные примеры:\nThe ground was wet — it had been raining all night. How long had they been living together before they got married? I had been feeling ill for days before I saw a doctor. She was tired because she had been travelling for twelve hours. We hadn't been talking for long when he mentioned the accident. He had been saving money for years before he bought the house.\n\nЧастые ошибки:\n— Путаница с Past Perfect Simple: если важен результат/факт — Simple ('I had finished the report'), если процесс/длительность — Continuous ('I had been writing the report for hours').\n— Пропуск been: 'I had working there' — неправильно, нужно 'I had been working there'.\n— Использование со state-глаголами: 'I had been knowing her' — неправильно, нужно 'I had known her'.",
            ],
            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Future Continuous',
                'category' => 'Tenses',
                'theory' => "Future Continuous описывает действие, которое будет в процессе в конкретный момент будущего, а также запланированные события, которые произойдут в обычном порядке вещей.\n\nКогда используется:\n— действие в процессе в указанный момент будущего: This time next week, I'll be lying on a beach.\n— запланированное действие без особого намерения, просто в силу расписания/рутины: I'll be seeing my boss tomorrow, so I'll ask her then.\n— вежливый вопрос о чужих планах: Will you be using the printer soon?\n— два параллельных действия в будущем: While I'm working, she'll be studying.\n\nСтруктура — утверждение:\nПодлежащее + will + be + глагол-ing.\nI will be travelling. They'll be waiting for us.\n\nСтруктура — отрицание:\nПодлежащее + will not (won't) + be + глагол-ing.\nI won't be attending the conference this year.\n\nСтруктура — вопрос:\nWill + подлежащее + be + глагол-ing?\nWill you be staying for dinner? What will you be doing this time tomorrow?\n\nПримеры:\nThis time tomorrow, I'll be sitting on a plane to New York. She'll be working late tonight. Will you be needing the car this afternoon? We'll be celebrating our anniversary next week. I won't be answering emails during my holiday. They'll be arriving around 6 p.m.\n\nДополнительные примеры:\nAt 9 a.m. tomorrow, I'll be taking my exam. Will you be joining us for lunch? He'll be giving a presentation at the conference. This time next year, we'll be living in a new house. I'll be thinking of you during the trip. She won't be coming to the meeting — she's on holiday.\n\nЧастые ошибки:\n— Future Continuous вместо Future Simple для одномоментного действия: 'I will be calling you at 5' звучит естественно только если звонок — часть процесса/рутины; для простого обещания достаточно 'I will call you at 5'.\n— Пропуск be: 'I will working' — неправильно, нужно 'I will be working'.\n— Путаница с going to: Future Continuous не выражает намерение, а описывает процесс или естественный ход событий — не 'I will be buying a car' в смысле плана (лучше going to), а именно процесс в конкретный момент.",
            ],
            [
                'level' => 'B2',
                'lesson' => '',
                'title' => 'Future Perfect',
                'category' => 'Tenses',
                'theory' => "Future Perfect используется, чтобы показать, что действие будет полностью завершено к определённому моменту в будущем.\n\nКогда используется:\n— завершённость действия к конкретному будущему моменту: By 6 p.m., I will have finished all my work.\n— подсчёт итогов к определённой дате: By 2030, she will have worked here for twenty years.\n— предположение о том, что уже произошло к настоящему моменту (реже): He will have arrived by now, I think.\n\nСтруктура — утверждение:\nПодлежащее + will + have + глагол в 3-й форме.\nI will have finished. They will have left by then.\n\nСтруктура — отрицание:\nПодлежащее + will not (won't) + have + глагол в 3-й форме.\nShe won't have finished the report by Friday.\n\nСтруктура — вопрос:\nWill + подлежащее + have + глагол в 3-й форме?\nWill you have completed the course by June?\n\nПримеры:\nBy the time you read this, I will have already left. She will have graduated by next summer. Will they have finished building the house by December? We won't have saved enough money by then. By 10 p.m., I will have watched the whole series. He will have retired by the time his daughter finishes university.\n\nДополнительные примеры:\nBy next month, we will have lived here for exactly one year. Will you have eaten by the time I get home? I will have sent the documents by tomorrow morning. By the end of the year, sales will have doubled. She won't have arrived yet — her flight lands at 9. By the time he's thirty, he will have visited every continent.\n\nЧастые ошибки:\n— Пропуск have: 'I will finished by then' — неправильно, нужно 'I will have finished by then'.\n— Future Perfect вместо Future Simple без указания завершённости к моменту: если просто говорите о будущем факте без 'к какому-то времени', используйте Future Simple, а не Perfect.\n— Забытая 3-я форма глагола: 'I will have go' — неправильно, нужно 'I will have gone'.",
            ],
            [
                'level' => 'C1',
                'lesson' => '',
                'title' => 'Future Perfect Continuous',
                'category' => 'Tenses',
                'theory' => "Future Perfect Continuous показывает, что действие будет длиться в течение определённого периода вплоть до конкретного момента в будущем — акцент на процессе и его продолжительности.\n\nКогда используется:\n— длительность процесса, которая будет достигнута к определённому будущему моменту: By June, I will have been working here for five years.\n— объяснение будущего состояния через процесс, который к тому моменту уже долго идёт: By midnight, we will have been driving for ten hours.\n— используется реже других времён, в основном в формальном/письменном стиле.\n\nСтруктура — утверждение:\nПодлежащее + will + have + been + глагол-ing.\nI will have been working. She will have been studying for six hours.\n\nСтруктура — отрицание:\nПодлежащее + will not (won't) + have + been + глагол-ing.\nBy then, he won't have been living there very long.\n\nСтруктура — вопрос:\nWill + подлежащее + have + been + глагол-ing?\nHow long will you have been studying by the time you graduate?\n\nПримеры:\nBy next year, I will have been teaching for a decade. In an hour, we will have been waiting for three hours. By the time she arrives, I will have been cooking all day. Will you have been working here for five years by December? He won't have been living in Paris for very long by then. By midnight, they will have been travelling for twenty hours.\n\nДополнительные примеры:\nBy the end of this year, we will have been married for ten years. How long will you have been driving by the time you reach the coast? By 2027, the company will have been operating for fifty years. She will have been studying medicine for six years by the time she graduates. By next week, I will have been using this phone for exactly two years. They won't have been dating long enough to move in together by then.\n\nЧастые ошибки:\n— Пропуск been: 'I will have working' — неправильно, нужно 'I will have been working'.\n— Использование этого времени без указания периода/длительности — тогда естественнее звучит обычный Future Perfect: если важен просто факт завершения, используйте 'I will have finished', а не Continuous.\n— Путаница порядка вспомогательных глаголов: правильный порядок — will + have + been + -ing, не менять местами.",
            ],
            [
                'level' => 'A2',
                'lesson' => '',
                'title' => 'Future with Going To',
                'category' => 'Tenses',
                'theory' => "Going to используется для описания будущих планов и намерений, а также предсказаний, основанных на видимых сейчас признаках.\n\n"
                    ."Формула: am/is/are + going to + глагол в начальной форме.\n"
                    ."Планы и намерения: I am going to study medicine after school. (решение уже принято заранее)\n\n"
                    ."Предсказания на основе текущей ситуации: Look at those clouds! It's going to rain. (видим признаки прямо сейчас)\n\n"
                    ."Отрицание и вопрос: She isn't going to come to the party. Are you going to buy a new phone?\n\n"
                    ."Сравнение с will: going to — заранее спланированное намерение или очевидное по признакам предсказание; will — спонтанное решение в момент речи. 'I'm thirsty.' — 'I'll get you some water.' (спонтанно) vs 'I've decided — I'm going to learn Spanish this year.' (план).",
            ],
            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Past with Going To',
                'category' => 'Tenses',
                'theory' => "Was/were going to описывает намерение или план в прошлом, который обычно не был реализован — что-то помешало.\n\n"
                    ."Формула: was/were + going to + глагол в начальной форме.\n"
                    ."Пример: I was going to call you, but I forgot. (собирался, но не сделал)\n\n"
                    ."Также используется для предсказания в прошлом относительно ещё более раннего момента: The sky was dark — it was going to storm. (было понятно по признакам, что вот-вот начнётся буря)\n\n"
                    ."Часто сопровождается объяснением причины, почему план не сработал, через but: We were going to go to the beach, but it started raining.\n\n"
                    ."Отличие от простых намерений (wanted to, planned to): was/were going to подчёркивает, что подготовка к действию уже была на конкретной стадии, а не просто общее желание.",
            ],
            [
                'level' => 'A2',
                'lesson' => '',
                'title' => 'Talking about the Present',
                'category' => 'Tenses',
                'theory' => "Чтобы говорить о настоящем, в английском используется несколько разных времён — выбор зависит от того, что именно вы хотите сказать.\n\n"
                    ."Present Simple — для фактов, привычек, постоянных состояний: I live in Tashkent. Water boils at 100°C.\n\n"
                    ."Present Continuous — для действий, происходящих прямо сейчас, или временных ситуаций: I am reading a book right now. She is staying with her parents this month.\n\n"
                    ."Present Perfect — для связи прошлого опыта или недавнего действия с настоящим моментом: I have already eaten. Have you ever been to Japan?\n\n"
                    ."Present Perfect Continuous — чтобы подчеркнуть длительность действия, которое продолжается сейчас: I have been working on this project for two weeks.\n\n"
                    ."Некоторые глаголы состояния (state verbs: know, want, believe, love, own) обычно не используются в Continuous формах: I know the answer (не am knowing).",
            ],
            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Talking about the Past',
                'category' => 'Tenses',
                'theory' => "Для рассказа о прошлом английский язык предлагает несколько времён и конструкций, в зависимости от того, что важно подчеркнуть.\n\n"
                    ."Past Simple — для завершённых действий в конкретный момент прошлого: I visited Rome in 2019.\n\n"
                    ."Past Continuous — для действия в процессе в определённый момент прошлого, часто как фон для другого действия: I was cooking when the phone rang.\n\n"
                    ."Past Perfect — для действия, которое произошло раньше другого события в прошлом: She had left before I arrived.\n\n"
                    ."Used to и would — для повторяющихся действий или состояний в прошлом, которые больше не происходят: I used to play football every weekend. We would visit our grandparents every summer. (would — только для действий, не для состояний: 'I used to live in London', а не 'I would live')\n\n"
                    ."Выбор времени зависит от контекста: если важен просто факт — Past Simple; если процесс — Past Continuous; если последовательность событий — Past Perfect.",
            ],
            [
                'level' => 'B1',
                'lesson' => 'Future Forms Review',
                'title' => 'Talking about the Future',
                'category' => 'Tenses',
                'theory' => "В английском нет отдельной грамматической формы «будущего времени» — вместо этого используется несколько конструкций в зависимости от смысла.\n\n"
                    ."Will — для предсказаний без явных доказательств, спонтанных решений, обещаний: I think it will rain tomorrow. I'll help you with that.\n\n"
                    ."Going to — для заранее спланированных намерений и предсказаний на основе видимых признаков: We're going to move to a new flat next month.\n\n"
                    ."Present Continuous — для договорённостей и запланированных событий с конкретным временем: I'm meeting my dentist at 3 p.m. on Friday.\n\n"
                    ."Present Simple — для расписаний и графиков (поезда, самолёты, программы): The train leaves at 9:15 tomorrow.\n\n"
                    ."Future Continuous и Future Perfect — для действий в процессе или уже завершённых к определённому моменту в будущем (см. отдельные темы).",
            ],
            [
                'level' => 'C1',
                'lesson' => '',
                'title' => 'Aspects',
                'category' => 'Tenses',
                'theory' => "Aspect (вид/аспект) — это грамматическая категория, которая показывает, как протекает действие во времени, в отличие от tense (времени), которое показывает, когда оно происходит.\n\n"
                    ."В английском есть четыре аспекта, которые комбинируются с тремя временами (present/past/future), образуя 12 времён.\n\n"
                    ."Simple aspect — действие представлено как факт, без внимания к процессу: I write, I wrote, I will write.\n\n"
                    ."Continuous (progressive) aspect — действие представлено как процесс, происходящий в определённый момент: I am writing, I was writing, I will be writing.\n\n"
                    ."Perfect aspect — действие представлено как завершённое к определённому моменту, с акцентом на результат или связь с этим моментом: I have written, I had written, I will have written.\n\n"
                    ."Perfect continuous aspect — сочетает завершённость к моменту и длительность процесса до этого момента: I have been writing, I had been writing, I will have been writing.\n\n"
                    ."Понимание aspect как отдельной от tense категории помогает системно понять всю систему из 12 времён, а не запоминать каждое время отдельно.",
            ],
            [
                'level' => 'A1',
                'lesson' => '',
                'title' => 'Proper and Common Nouns',
                'category' => 'Nouns',
                'theory' => "Proper nouns (собственные) называют конкретного, уникального человека, место или вещь и всегда пишутся с заглавной буквы. Common nouns (нарицательные) называют общий класс предметов или людей.\n\nКогда используется:\n— proper noun для конкретного имени: London, Sarah, Monday, the Eiffel Tower.\n— common noun для общего названия класса объектов: city, girl, day, tower.\n— артикль the часто используется с некоторыми proper nouns (реки, горные цепи, страны во множественном числе): the Nile, the Alps, the Netherlands.\n\nСтруктура:\nProper nouns пишутся с заглавной буквы независимо от позиции в предложении: I met John in Paris last June.\n\nПримеры:\nCommon: city, book, dog, teacher, country. Proper: New York, Harry Potter, Max, Ms. Johnson, Japan.\n\nДополнительные примеры:\nShe was born in Spain. My favourite month is December. He reads the Bible every Sunday. We visited the Louvre in Paris.\n\nЧастые ошибки:\n— Строчная буква у имён собственных: 'i met sarah in london' — неправильно, нужно 'I met Sarah in London'.\n— Заглавная буква у нарицательных существительных без причины: 'I bought a New Book' — неправильно, нужно 'I bought a new book'.",
            ],
            [
                'level' => 'A2',
                'lesson' => 'Countable and Uncountable Nouns',
                'title' => 'Countable and Uncountable Nouns',
                'category' => 'Nouns',
                'theory' => "Countable nouns (исчисляемые) можно посчитать и использовать во множественном числе. Uncountable nouns (неисчисляемые) обозначают вещества, качества или понятия, которые нельзя посчитать поштучно.\n\nКогда используется:\n— countable — для отдельных, счётных предметов: one apple, two apples.\n— uncountable — для веществ, жидкостей, абстрактных понятий: water, rice, advice, information, furniture.\n— many/few — с исчисляемыми; much/little — с неисчисляемыми: many books, much water.\n— для счёта неисчисляемых используются слова-счётчики: a piece of advice, a glass of water, a loaf of bread.\n\nФормы:\nCountable: singular + plural (book/books). Uncountable: только одна форма, без множественного числа, не используется с a/an: information (не an information, не informations).\n\nПримеры:\nI have three books. She gave me some advice. There isn't much time left. How many apples do you want? We need a bit of sugar. There are a lot of chairs in the room.\n\nДополнительные примеры:\nI'd like a cup of coffee, please. He has very little money. There are many opportunities here. I need some information about the course. Can you give me a few examples?\n\nЧастые ошибки:\n— Множественное число у неисчисляемых: 'informations', 'advices', 'furnitures' — неправильно; information, advice, furniture не имеют формы множественного числа.\n— many вместо much с неисчисляемыми: 'How many money do you have?' — неправильно, нужно 'How much money do you have?'.",
            ],
            [
                'level' => 'A1',
                'lesson' => 'Plural Nouns',
                'title' => 'Singular and Plural',
                'category' => 'Nouns',
                'theory' => "Singular (единственное число) — один предмет, plural (множественное число) — два и более. Форма существительного зависит от его числа.\n\nКогда используется:\n— singular для одного предмета или человека: one cat, a book.\n— plural для двух и более: two cats, three books.\n— некоторые существительные всегда во множественном числе (см. тему Plural-Only Nouns), некоторые не меняются вовсе (sheep, fish).\n\nФормы (образование множественного числа):\nБольшинство: + -s (cat → cats). После -s/-ss/-sh/-ch/-x/-o: + -es (box → boxes, potato → potatoes). После согласной + y: y → ies (city → cities). После -f/-fe: часто f/fe → ves (leaf → leaves, knife → knives). Неправильные формы: man → men, child → children, foot → feet, mouse → mice, person → people.\n\nПримеры:\none dog — two dogs. one box — five boxes. one baby — three babies. one child — two children. one leaf — many leaves. one sheep — twenty sheep (не меняется).\n\nДополнительные примеры:\nThere are four buses at the station. She has two knives in the kitchen. I saw three mice in the garden. Many countries have their own currency. The women were talking outside.\n\nЧастые ошибки:\n— Регулярное окончание -s у неправильных форм: 'childs', 'mans', 'foots' — неправильно, нужно children, men, feet.\n— Пропуск -es после шипящих: 'boxs' — неправильно, нужно 'boxes'.",
            ],
            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Plural-Only Nouns',
                'category' => 'Nouns',
                'theory' => "Plural-only nouns — существительные, которые всегда употребляются только во множественном числе и не имеют формы единственного числа с тем же значением.\n\nКогда используется:\n— предметы, состоящие из двух одинаковых частей: trousers, scissors, glasses, jeans, shorts.\n— некоторые слова, обозначающие категории вещей: clothes, goods, belongings.\n— всегда согласуются с глаголом во множественном числе: My trousers are too tight.\n\nФормы:\nЕсли нужно посчитать такой предмет, используется 'a pair of': a pair of scissors, two pairs of jeans.\n\nПримеры:\nMy glasses are broken. These scissors are very sharp. Where are my trousers? I bought new jeans yesterday. All my clothes are in the wardrobe. The police are investigating the case.\n\nДополнительные примеры:\nI need a new pair of shorts. Her belongings were left at the hotel. The stairs are quite steep. Where did you put the tongs? The goods arrived yesterday.\n\nЧастые ошибки:\n— Использование единственного числа глагола: 'My trousers is dirty' — неправильно, нужно 'My trousers are dirty'.\n— Употребление a/an напрямую перед plural-only существительным: 'a scissors' — неправильно, нужно 'a pair of scissors'.",
            ],
            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Collective Nouns',
                'category' => 'Nouns',
                'theory' => "Collective nouns (собирательные существительные) обозначают группу людей, животных или предметов как единое целое: team, family, government, audience, herd.\n\nКогда используется:\n— чтобы назвать группу как единый организованный коллектив: The team is playing well.\n— в британском варианте английского собирательные существительные часто согласуются с глаголом во множественном числе, когда речь о членах группы как отдельных людях: The team are arguing among themselves.\n— в американском варианте почти всегда используется единственное число: The team is playing well.\n\nФормы:\nПримеры собирательных существительных: family, team, government, staff, audience, crowd, herd (стадо), flock (стая), jury.\n\nПримеры:\nMy family is very supportive. The audience was silent during the film. The government has announced new plans. A herd of elephants crossed the road. The jury is still deliberating. Our staff works hard every day.\n\nДополнительные примеры:\nThe band are recording a new album. The committee has made its decision. A flock of birds flew overhead. The crowd was cheering loudly. The class is going on a trip next week.\n\nЧастые ошибки:\n— Смешение единственного и множественного числа в одном предложении: лучше выбрать один вариант согласования и придерживаться его во всём предложении.\n— Путаница собирательного существительного с обычным множественным числом: family (одна семья, собирательное) отличается от families (несколько семей, обычное множественное).",
            ],
            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Abstract and Concrete Nouns',
                'category' => 'Nouns',
                'theory' => "Concrete nouns (конкретные) называют предметы, которые можно увидеть, потрогать, услышать — физически ощутимые. Abstract nouns (абстрактные) называют идеи, качества, чувства, состояния — то, что нельзя воспринять органами чувств.\n\nКогда используется:\n— concrete для физических объектов: table, dog, rain, music (можно услышать).\n— abstract для понятий, эмоций, качеств: love, freedom, honesty, courage, happiness.\n— многие abstract nouns образуются от прилагательных или глаголов с помощью суффиксов: happy → happiness, free → freedom, decide → decision.\n\nФормы (типичные суффиксы абстрактных существительных):\n-ness (kindness), -ity (creativity), -tion/-sion (decision), -ance/-ence (patience), -ism (optimism), -ship (friendship).\n\nПримеры:\nConcrete: chair, apple, river, phone, teacher. Abstract: happiness, freedom, courage, knowledge, friendship.\n\nДополнительные примеры:\nHonesty is the best policy. I could smell fresh bread in the kitchen. Her kindness touched everyone. The bridge was built in 1990. True happiness comes from within.\n\nЧастые ошибки:\n— Использование артикля a/an с абстрактными существительными в общем смысле: 'I want a happiness' — обычно неправильно, нужно 'I want happiness'.\n— Путаница суффиксов: 'happyness' — неправильно, нужно 'happiness'.",
            ],
            [
                'level' => 'A2',
                'lesson' => '',
                'title' => 'Gender-specific Nouns',
                'category' => 'Nouns',
                'theory' => "Gender-specific nouns — существительные, которые указывают на пол человека или животного, в отличие от gender-neutral nouns, которые подходят для любого пола.\n\nКогда используется:\n— когда пол важен для смысла или является частью традиционного названия профессии/роли: actor/actress, prince/princess.\n— в современном английском предпочтение часто отдаётся гендерно-нейтральным вариантам, особенно для профессий: actor (для любого пола), police officer вместо policeman.\n\nФормы (пары мужской/женский род):\nman/woman, boy/girl, king/queen, actor/actress, waiter/waitress, husband/wife, uncle/aunt, host/hostess, hero/heroine.\n\nПримеры:\nThe prince married the princess. My uncle and aunt live abroad. The waiter brought our food. She works as a police officer. The lion and the lioness were resting.\n\nДополнительные примеры:\nThe king and queen attended the ceremony. My grandson and granddaughter are twins. The actor won an award for his role. The hero saved the town. Every host and hostess welcomed the guests warmly.\n\nЧастые ошибки:\n— Использование устаревших гендерных форм в официальном контексте, где предпочтителен нейтральный вариант: 'chairman' часто заменяют на 'chairperson' или 'chair'.\n— Неверное образование женского рода: 'actoress' — неправильно, нужно 'actress'.",
            ],
            [
                'level' => 'B2',
                'lesson' => '',
                'title' => 'Noun Modifiers',
                'category' => 'Nouns',
                'theory' => "Noun modifiers — слова, которые определяют, уточняют или описывают существительное: прилагательные, другие существительные (noun + noun), причастия, определители.\n\nКогда используется:\n— прилагательное перед существительным: a beautiful garden.\n— существительное в роли определения другого существительного: a garden gate, a coffee cup, a summer holiday.\n— причастие как определение: a sleeping baby, a broken window.\n— несколько модификаторов подряд в определённом порядке.\n\nФормы (порядок прилагательных перед существительным):\nopinion → size → age → shape → colour → origin → material → purpose + noun. Пример: a beautiful small old round black Italian leather handbag.\n\nПримеры:\na wooden table (материал). a summer dress (назначение/сезон). a broken chair (причастие). a beautiful old house (мнение + возраст). a large red apple (размер + цвет). the sleeping cat (причастие).\n\nДополнительные примеры:\nShe wore a stunning long red evening dress. He bought a small black leather wallet. The falling leaves covered the path. A crying baby woke us up. We stayed in a charming old French village.\n\nЧастые ошибки:\n— Неправильный порядок прилагательных: 'a red big apple' звучит неестественно, нужно 'a big red apple'.\n— Множественное число у существительного-модификатора: 'a shoes shop' — неправильно, нужно 'a shoe shop'.",
            ],
            [
                'level' => 'A2',
                'lesson' => '',
                'title' => 'Possessive Form of Nouns',
                'category' => 'Nouns',
                'theory' => "Possessive form (притяжательный падеж) показывает принадлежность одного существительного другому.\n\nКогда используется:\n— чтобы показать, кому что принадлежит: Anna's book, the dog's tail.\n— для людей и животных чаще используется 's, для неодушевлённых предметов чаще предлог of: the roof of the house.\n— для указания времени и расстояния: a week's holiday, two hours' drive.\n\nФормы:\nЕдинственное число: + 's (Tom's car). Множественное число, оканчивающееся на -s: + ' (the girls' room). Множественное число без -s: + 's (the children's toys). Имена, оканчивающиеся на -s: обычно + 's (James's book) или + ' (James').\n\nПримеры:\nThis is Maria's phone. The children's toys are everywhere. My parents' house is huge. The cat's tail is black. James's car is new. The students' results were excellent.\n\nДополнительные примеры:\nThat's my sister's bag. The company's profits increased this year. The teachers' meeting starts at 9. Charles's book is on the table. Where is the manager's office?\n\nЧастые ошибки:\n— Апостроф перед -s у существительного во множественном числе, уже оканчивающегося на -s: 'the girls's room' — неправильно, нужно 'the girls' room'.\n— Путаница possessive с обычным множественным числом: 'The dog's are barking' — неправильно, нужно 'The dogs are barking'.",
            ],
            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Compound Nouns',
                'category' => 'Nouns',
                'theory' => "Compound nouns (составные существительные) образуются из двух или более слов, которые вместе выражают одно понятие.\n\nКогда используется:\n— чтобы назвать понятие, для которого нет отдельного простого слова: a toothbrush, a bus stop, mother-in-law.\n— первое слово обычно уточняет или описывает второе (главное) слово: a coffee cup — чашка для кофе.\n\nФормы (три способа написания):\nСлитно: toothbrush, notebook, sunflower. Через дефис: mother-in-law, well-known, check-in. Раздельно: bus stop, credit card, swimming pool. Единого правила нет — нужно запоминать написание конкретного слова.\n\nПримеры:\ntoothpaste, football, bus stop, mother-in-law, credit card, sunrise, blackboard, swimming pool.\n\nДополнительные примеры:\nShe bought a new washing machine. My brother-in-law is a doctor. We need a fire extinguisher in the kitchen. The traffic light turned red. He works as a part-time babysitter.\n\nЧастые ошибки:\n— Множественное число не в том месте составного слова: 'mother-in-laws' — неправильно, нужно 'mothers-in-law'.\n— Смешение слитного и раздельного написания — всегда стоит проверять по словарю.",
            ],
            [
                'level' => 'B1',
                'lesson' => 'Gerunds vs Infinitives',
                'title' => 'Gerunds',
                'category' => 'Nouns',
                'theory' => "Gerund — форма глагола на -ing, которая используется как существительное: называет действие или процесс, но ведёт себя в предложении как noun.\n\nКогда используется:\n— как подлежащее предложения: Swimming is good for your health.\n— как дополнение после определённых глаголов (enjoy, avoid, finish, suggest, mind): I enjoy reading.\n— после предлогов: She is interested in learning Spanish.\n— после некоторых устойчивых выражений: It's no use crying over spilt milk.\n\nФорма и образование:\nглагол + -ing (walk → walking, run → running (удвоение согласной), write → writing (убираем немую -e)).\n\nПримеры:\nSmoking is bad for your health. I love cooking on weekends. She's good at painting. Thank you for helping me. He avoided answering the question. Reading books expands your vocabulary.\n\nДополнительные примеры:\nWould you mind closing the window? I'm thinking of moving to another city. Playing video games is his favourite hobby. We finished decorating the house. She's tired of waiting.\n\nЧастые ошибки:\n— Использование to + infinitive после глаголов, требующих gerund: 'I enjoy to read' — неправильно, нужно 'I enjoy reading'.\n— Gerund после предлога заменяется infinitive по ошибке: 'She's interested to learn' — неправильно, нужно 'She's interested in learning'.",
            ],
            [
                'level' => 'B2',
                'lesson' => '',
                'title' => 'Verbal Nouns',
                'category' => 'Nouns',
                'theory' => "Verbal nouns — общий термин для существительных, образованных от глаголов и обозначающих действие или процесс. Gerund (-ing) — один из типов verbal noun, но есть и другие формы с суффиксами.\n\nКогда используется:\n— чтобы превратить действие/процесс в существительное, о котором можно говорить как о предмете: the arrival of the train, a decision, an agreement.\n— часто используется в формальном, письменном и академическом стиле.\n\nФормы (типичные суффиксы):\n-tion/-sion: decide → decision, inform → information. -ment: develop → development, agree → agreement. -al: arrive → arrival, refuse → refusal. -ing (gerund): read → reading.\n\nПримеры:\nThe decision surprised everyone. Their agreement was signed yesterday. The arrival of the guests was delayed. Government investment in education is increasing. His refusal to apologise upset her.\n\nДополнительные примеры:\nThe development of the new drug took years. Their movement was quick and quiet. The explanation was very clear. The construction of the bridge finished last month. Her improvement in English is remarkable.\n\nЧастые ошибки:\n— Использование глагола вместо verbal noun там, где грамматически нужно существительное: 'The develop of the city' — неправильно, нужно 'The development of the city'.\n— Неправильный суффикс: 'the decideion' — неправильно, нужно 'the decision'.",
            ],
            [
                'level' => 'C1',
                'lesson' => '',
                'title' => 'Nominal Adjectives',
                'category' => 'Nouns',
                'theory' => "Nominal adjectives (субстантивированные прилагательные) — прилагательные, которые используются как существительные, обычно с определённым артиклем the, для обозначения целой группы людей.\n\nКогда используется:\n— the + прилагательное для обозначения группы людей с общим признаком: the rich, the poor, the elderly, the unemployed, the blind.\n— такие конструкции всегда согласуются с глаголом во множественном числе: The rich are not always happy.\n— иногда the + прилагательное обозначает абстрактное понятие (реже): the unknown, the impossible.\n\nФормы:\nthe + прилагательное (без последующего существительного и без -s): the rich, the young, the disabled, the injured.\n\nПримеры:\nThe government should help the poor. The elderly need special care. The wounded were taken to hospital. The unemployed are struggling to find jobs. She always fights for the oppressed. The brave deserve recognition.\n\nДополнительные примеры:\nThe rich often forget how the other half live. Charities support the homeless. The young are usually more open to change. The blind rely on their other senses. Society must protect the vulnerable.\n\nЧастые ошибки:\n— Добавление -s к nominal adjective: 'the riches' меняет значение — нужно просто 'the rich'.\n— Согласование глагола в единственном числе: 'The poor is suffering' — неправильно, нужно 'The poor are suffering'.",
            ],
            [
                'level' => 'B2',
                'lesson' => '',
                'title' => 'Noun Clauses',
                'category' => 'Nouns',
                'theory' => "Noun clause — придаточное предложение, которое выполняет в предложении роль существительного: может быть подлежащим, дополнением или именной частью сказуемого.\n\nКогда используется:\n— как подлежащее: What she said surprised me.\n— как дополнение после глагола: I don't know where he lives.\n— как дополнение после прилагательного: I'm not sure if this is correct.\n— после предлога: I'm interested in what you think.\n\nФормы (вводные слова noun clause):\nthat, if/whether (для да/нет вопросов), вопросительные слова (what, where, when, why, how, who), а также what/whoever и т.д.\n\nПримеры:\nI believe that he is right. Do you know if she is coming? What you need is more practice. I wonder why he left early. She asked where I had been. It's important that everyone attends.\n\nДополнительные примеры:\nI'm not sure whether this is the right decision. What happened next surprised everyone. He explained how the machine works. I doubt that she will agree. Whoever wins the competition gets a prize.\n\nЧастые ошибки:\n— Прямой порядок слов теряется в косвенном вопросе: 'I don't know where is he' — неправильно, нужно 'I don't know where he is'.\n— Пропуск if/whether в косвенном да/нет вопросе: 'I asked he was coming' — неправильно, нужно 'I asked if/whether he was coming'.",
            ],
            [
                'level' => 'B2',
                'lesson' => '',
                'title' => 'Gerund Phrases',
                'category' => 'Nouns',
                'theory' => "Gerund phrase — словосочетание, в котором gerund (глагол на -ing) выступает главным словом вместе со своими дополнениями и определениями, и вся конструкция играет роль существительного.\n\nКогда используется:\n— как подлежащее: Learning a new language takes time and patience.\n— как дополнение: I enjoy spending time with my family.\n— после предлога: She left without saying goodbye.\n— с притяжательным местоимением/существительным перед gerund в формальном стиле: I appreciate your helping me.\n\nФормы:\ngerund + дополнение/обстоятельство: reading books, driving to work, being late, having finished the project.\n\nПримеры:\nEating too much sugar is bad for you. I love listening to music in the evening. Thank you for coming to my party. She's afraid of flying alone. Winning the championship was a huge achievement. He apologised for being late.\n\nДополнительные примеры:\nStudying abroad changed my perspective on life. I can't imagine living without my phone. They talked about moving to another country. Playing sports every day keeps you healthy. I remember meeting him for the first time.\n\nЧастые ошибки:\n— Использование infinitive вместо gerund phrase после предлога: 'without to say goodbye' — неправильно, нужно 'without saying goodbye'.\n— Неполная фраза без нужного дополнения может звучать неестественно, если контекст явно требует уточнения.",
            ],

            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Adverb Placement and Order',
                'category' => 'Adverbs',
                'theory' => "От места наречия в предложении может зависеть смысл или естественность фразы. Разные типы наречий тяготеют к разным позициям.\n\nКогда используется:\n— front position (в начале предложения) — часто для наречий времени или для связи с предыдущим предложением: Yesterday, I met an old friend.\n— mid position (перед смысловым глаголом, но после to be/вспомогательного) — типично для наречий частоты и степени: She often visits her parents. I have already finished.\n— end position (в конце предложения) — типично для наречий образа действия, места и времени: She sings beautifully. We arrived late.\n\nСтруктура (порядок нескольких наречий в конце предложения):\nManner → Place → Time (образ действия → место → время): She sang beautifully at the concert last night.\n\nПримеры:\nHe quickly finished his homework. I have never seen anything like this. We played football in the park yesterday. Honestly, I don't agree. She always arrives on time. They walked slowly through the forest.\n\nДополнительные примеры:\nFortunately, nobody was hurt. She carefully wrapped the gift. I usually wake up early. He works hard every day. Suddenly, the lights went out.\n\nЧастые ошибки:\n— Наречие частоты в конце вместо середины предложения: 'I go always to school by bus' — неправильно, нужно 'I always go to school by bus'.\n— Наречие между глаголом и прямым дополнением: 'She speaks fluently English' — неправильно, нужно 'She speaks English fluently'.",
            ],
            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Comparative and Superlative Adverbs',
                'category' => 'Adverbs',
                'theory' => "Как и прилагательные, многие наречия имеют сравнительную и превосходную степень для сравнения того, КАК происходят действия.\n\nКогда используется:\n— сравнение двух действий: He runs faster than his brother.\n— выделение одного варианта среди многих: She works the hardest of the team.\n\nФормы:\nКороткие наречия (fast, hard, late, early): + -er / + -est (fast → faster → fastest). Наречия на -ly: more/most (carefully → more carefully → most carefully). Неправильные формы: well → better → best; badly → worse → worst; far → further → furthest.\n\nПримеры:\nShe runs faster than me. He arrived earlier than expected. This team plays more skilfully than the other. Of all of us, she works the hardest. He sings the best in the choir. My English has improved a lot, but hers has improved more.\n\nДополнительные примеры:\nYou need to speak more clearly. He drives more carefully than his sister. This machine works better than the old one. She finished the race first, running fastest of all. I sleep worse when it's hot.\n\nЧастые ошибки:\n— Двойное сравнение: 'more faster' — неправильно, нужно просто 'faster'.\n— Использование прилагательного вместо наречия в сравнении: 'She sings more good than him' — неправильно, нужно 'She sings better than him'.",
            ],
            [
                'level' => 'A2',
                'lesson' => 'Adjectives and Adverbs: Formation',
                'title' => 'Types of Adverb',
                'category' => 'Adverbs',
                'theory' => "Наречия делятся на несколько типов по значению — каждый тип отвечает на свой вопрос о действии.\n\nКогда используется:\n— manner (как?): carefully, quickly, well.\n— place (где?): here, there, outside.\n— time (когда?): now, yesterday, soon.\n— frequency (как часто?): always, often, never.\n— degree (в какой степени?): very, quite, extremely.\n— probability (насколько вероятно?): probably, certainly, maybe.\n\nФормы (образование от прилагательных):\nприлагательное + -ly: quick → quickly, careful → carefully. -y меняется на -i + ly: happy → happily. -le меняется на -ly: gentle → gently. Исключения: good → well, fast → fast, hard → hard.\n\nПримеры:\nShe speaks slowly and clearly. We live nearby. I'll call you later. He always tells the truth. It's incredibly hot today. They will probably arrive late.\n\nДополнительные примеры:\nHe answered the question correctly. Put it there, please. I saw her recently. She rarely gets angry. This is extremely useful. It's certainly possible.\n\nЧастые ошибки:\n— good вместо well как наречие: 'He plays football good' — неправильно, нужно 'He plays football well'.\n— Лишнее -ly у уже готовых наречий: 'fastly' — неправильно, fast уже является наречием само по себе.",
            ],
            [
                'level' => 'A1',
                'lesson' => '',
                'title' => 'Adverb of Place',
                'category' => 'Adverbs',
                'theory' => "Adverbs of place отвечают на вопрос «где?» и показывают место или направление действия.\n\nКогда используется:\n— чтобы указать, где происходит действие: They live nearby.\n— чтобы указать направление движения: She looked up.\n— обычно стоят в конце предложения, иногда в начале для акцента: Here comes the bus!\n\nФормы:\nhere, there, everywhere, nowhere, somewhere, anywhere, inside, outside, upstairs, downstairs, abroad, nearby, away.\n\nПримеры:\nPut the box there. I looked everywhere for my keys. Let's go outside. She studies abroad. The children are playing upstairs. I can't find my phone anywhere.\n\nДополнительные примеры:\nCome here, please. There's nowhere to sit. He works nearby. We stayed inside because of the rain. Is there a café somewhere near here?\n\nЧастые ошибки:\n— Использование предлога there где нужно наречие here/there без it: 'It is there a problem' — неправильно, нужно 'There is a problem' (это dummy pronoun there, другая конструкция).\n— Лишний предлог: 'go to outside' — неправильно, нужно 'go outside' (outside уже само по себе наречие места).",
            ],
            [
                'level' => 'A1',
                'lesson' => '',
                'title' => 'Adverb of Time',
                'category' => 'Adverbs',
                'theory' => "Adverbs of time отвечают на вопрос «когда?» и показывают, в какой момент или период происходит действие.\n\nКогда используется:\n— чтобы указать конкретное или относительное время действия: I'll see you tomorrow.\n— обычно стоят в конце предложения, но могут стоять в начале для акцента: Tomorrow, we're going to the beach.\n— already/yet/still чаще занимают mid-position (см. тему Adverb Placement and Order).\n\nФормы:\nnow, then, today, tomorrow, yesterday, soon, later, already, yet, still, recently, immediately, finally.\n\nПримеры:\nI'm busy right now. She called me yesterday. We'll finish the project soon. Have you finished yet? He's still waiting. I saw her recently. Finally, the rain stopped.\n\nДополнительные примеры:\nLet's meet later. I already know the answer. Call me immediately if anything happens. We're leaving tomorrow morning. He hasn't arrived yet.\n\nЧастые ошибки:\n— yet в утвердительном предложении вместо already: 'I have yet finished' — неправильно, нужно 'I have already finished' (yet обычно в вопросах/отрицаниях).\n— Неверное место already/still: 'I have finished already the report' лучше 'I have already finished the report'.",
            ],
            [
                'level' => 'A2',
                'lesson' => 'Adverbs of Frequency',
                'title' => 'Adverbs of Frequency',
                'category' => 'Adverbs',
                'theory' => "Adverbs of frequency отвечают на вопрос «как часто?» и показывают, насколько регулярно происходит действие.\n\nКогда используется:\n— с Present Simple для описания привычек и регулярных действий: I always drink coffee in the morning.\n— стоят перед смысловым глаголом, но после глагола to be и вспомогательных глаголов: She is never late. I have often wondered about that.\n\nФормы (от 100% до 0%):\nalways (всегда) → usually (обычно) → often (часто) → sometimes (иногда) → occasionally (изредка) → rarely/seldom (редко) → never (никогда).\n\nПримеры:\nI always brush my teeth before bed. She usually walks to work. We often eat out on Fridays. He sometimes forgets his keys. They rarely argue. I never eat meat.\n\nДополнительные примеры:\nShe is always on time. I have never been to Africa. We occasionally go to the theatre. He seldom complains about anything. Do you usually work on weekends?\n\nЧастые ошибки:\n— Наречие частоты после смыслового глагола: 'I drink always coffee' — неправильно, нужно 'I always drink coffee'.\n— Наречие частоты перед to be: 'I always am tired' — неправильно, нужно 'I am always tired'.",
            ],
            [
                'level' => 'A2',
                'lesson' => '',
                'title' => 'Adverbs of Manner',
                'category' => 'Adverbs',
                'theory' => "Adverbs of manner отвечают на вопрос «как?» и описывают, каким образом выполняется действие.\n\nКогда используется:\n— чтобы описать способ или качество выполнения действия: She sings beautifully.\n— обычно стоят после глагола или после прямого дополнения, не между ними: He drives the car carefully.\n\nФормы:\nБольшинство образуются от прилагательного + -ly: careful → carefully, quick → quickly, quiet → quietly. Неправильные формы: good → well, fast → fast, hard → hard.\n\nПримеры:\nShe speaks English fluently. He closed the door quietly. They worked hard all day. I slept badly last night. She answered the question confidently. He drives fast.\n\nДополнительные примеры:\nThe team played brilliantly. She smiled warmly at everyone. He solved the problem easily. They danced gracefully. I explained it clearly, but he still didn't understand.\n\nЧастые ошибки:\n— good вместо well: 'She sings very good' — неправильно, нужно 'She sings very well'.\n— Наречие между глаголом и прямым дополнением: 'He plays well the piano' — неправильно, нужно 'He plays the piano well'.",
            ],
            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Adverbs of Degree',
                'category' => 'Adverbs',
                'theory' => "Adverbs of degree показывают интенсивность или степень качества, действия или другого наречия — отвечают на вопрос «насколько?».\n\nКогда используется:\n— чтобы усилить прилагательное или наречие: very tired, extremely difficult.\n— чтобы ослабить или смягчить значение: quite good, a bit cold, slightly late.\n— некоторые (enough) ставятся ПОСЛЕ прилагательного: good enough, tall enough.\n\nФормы:\nУсиливающие: very, extremely, really, so, too. Ослабляющие: quite, rather, fairly, a little, slightly, a bit. Особые: enough (после прилагательного), almost, hardly.\n\nПримеры:\nThis exercise is very difficult. I'm quite tired today. She was extremely happy with the results. It's too cold to go outside. He's tall enough to reach the shelf. I almost forgot my keys.\n\nДополнительные примеры:\nThe film was rather boring. I'm a bit worried about the exam. She speaks English fairly well. It's slightly warmer today. He was so excited he couldn't sleep.\n\nЧастые ошибки:\n— enough перед прилагательным: 'enough good' — неправильно, нужно 'good enough' (enough ставится после прилагательного/наречия).\n— too вместо very: 'I'm too happy' в значении простого усиления неверно — too означает избыток («слишком»), для простого усиления нужно very.",
            ],
            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Adverbs of Probability',
                'category' => 'Adverbs',
                'theory' => "Adverbs of probability показывают, насколько говорящий уверен в том, что говорит — от полной уверенности до простого предположения.\n\nКогда используется:\n— чтобы выразить высокую уверенность: certainly, definitely, undoubtedly.\n— чтобы выразить умеренную уверенность: probably, likely.\n— чтобы выразить сомнение или неуверенность: perhaps, maybe, possibly.\n— обычно стоят в mid position (перед смысловым глаголом), но maybe/perhaps часто в начале предложения.\n\nФормы:\ncertainly, definitely, undoubtedly, probably, likely, perhaps, maybe, possibly.\n\nПримеры:\nShe will definitely come to the party. He's probably at home now. Maybe we should ask for help. It's possibly the best film I've seen this year. They will certainly agree. Perhaps she forgot about the meeting.\n\nДополнительные примеры:\nHe's undoubtedly the best player on the team. It will likely rain tomorrow. Perhaps I'm wrong about this. She's probably already asleep. Maybe he just needs more time.\n\nЧастые ошибки:\n— Неверное место maybe/perhaps: они обычно стоят в начале предложения, а не в середине, как probably/certainly: 'She maybe is right' лучше 'Maybe she is right' или 'She is probably right'.\n— Смешение уровня уверенности: 'I'm maybe sure' — неправильно, maybe и sure противоречат друг другу по смыслу.",
            ],
            [
                'level' => 'A2',
                'lesson' => '',
                'title' => 'Adverbs of Movement and Direction',
                'category' => 'Adverbs',
                'theory' => "Adverbs of movement and direction показывают, куда или в каком направлении происходит движение.\n\nКогда используется:\n— с глаголами движения, чтобы уточнить направление: Come here! Go away! Look up.\n— часто образуют фразовые глаголы вместе с глаголом: sit down, stand up, come in, go out.\n\nФормы:\nhere, there, up, down, in, out, away, forward, backward, ahead, along, across, back.\n\nПримеры:\nCome here, please. He walked away without saying a word. Please sit down. Look up at the sky. Go straight ahead and turn left. She turned back and smiled.\n\nДополнительные примеры:\nThe plane flew overhead. We drove across the country. Move forward, please. He climbed up the ladder. The cat ran outside.\n\nЧастые ошибки:\n— Лишний предлог после наречия направления: 'go in to the room' — неправильно, если контекст уже ясен, часто достаточно 'go in' или 'go into the room' (не смешивать оба варианта).\n— Путаница up/down с реальным направлением, а не только фразовым значением: важно понимать, что 'give up' — фразовый глагол (сдаться), а не буквальное движение вверх.",
            ],
            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Demonstrative Adverbs',
                'category' => 'Adverbs',
                'theory' => "Demonstrative adverbs — небольшая группа наречий (here, there, now, then), которые, подобно указательным местоимениям this/that, указывают на конкретное место или момент времени.\n\nКогда используется:\n— here/there для указания на место (близко/далеко): Put it here. Look over there.\n— now/then для указания на время (настоящий момент/другой момент): I'm busy now. I was younger then.\n— часто используются вместе с указательными местоимениями для усиления: this house here, that day back then.\n\nФормы:\nhere, there (место); now, then (время).\n\nПримеры:\nSign here, please. The keys are over there. I need to leave now. Back then, life was simpler. Come over here for a second. We didn't have phones then.\n\nДополнительные примеры:\nRight here is where it happened. There you go! Now is the perfect time to start. From then on, everything changed. Stay there until I come back.\n\nЧастые ошибки:\n— Путаница here/there с dummy pronoun there (there is/are) — это разные слова с разной функцией, несмотря на одинаковое написание.\n— Смешение now (настоящее) и then (прошлое/будущее) в одном временном контексте — важно, чтобы наречие соответствовало времени глагола в предложении.",
            ],
            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Relative Adverbs',
                'category' => 'Adverbs',
                'theory' => "Relative adverbs (where, when, why) вводят придаточные предложения, заменяя более громоздкую конструкцию 'preposition + which'.\n\nКогда используется:\n— where для места (= in/at which): This is the house where I grew up.\n— when для времени (= on/at which): I remember the day when we first met.\n— why для причины (= for which), обычно после reason: That's the reason why I left.\n\nСтруктура:\nRelative adverb заменяет 'preposition + which': the house in which I grew up = the house where I grew up.\n\nПримеры:\nThis is the town where I was born. I'll never forget the day when I got my first job. Nobody knows the reason why he left so suddenly. That's the restaurant where we had our first date. Summer is the season when the days are longest.\n\nДополнительные примеры:\nDo you remember the year when this happened? This is the office where I work. Tell me why you're upset. That's the moment when everything changed. I don't know the reason why she's angry.\n\nЧастые ошибки:\n— which/that вместо where для места: 'The house which I grew up' — неправильно (нужен предлог или where), нужно 'The house where I grew up' или 'The house in which I grew up'.\n— Лишний предлог после relative adverb: 'the house where I grew up in' — неправильно, лишний in (where уже включает значение предлога).",
            ],
            [
                'level' => 'A2',
                'lesson' => '',
                'title' => 'Interrogative Adverbs',
                'category' => 'Adverbs',
                'theory' => "Interrogative adverbs (where, when, why, how) используются для формирования специальных вопросов о месте, времени, причине и способе.\n\nКогда используется:\n— where — вопрос о месте: Where do you live?\n— when — вопрос о времени: When does the film start?\n— why — вопрос о причине: Why are you late?\n— how — вопрос о способе, состоянии или степени: How did you do that? How are you?\n\nСтруктура:\nВопросительное слово + вспомогательный глагол + подлежащее + смысловой глагол?\nWhere do you work? When did she arrive? Why didn't you call me? How does this machine work?\n\nПримеры:\nWhere is the nearest station? When will you be back? Why did you choose this job? How do you say this in English? How much does it cost? How often do you exercise?\n\nДополнительные примеры:\nWhere did you put my keys? When is your birthday? Why is the shop closed today? How long have you lived here? How far is the airport from here?\n\nЧастые ошибки:\n— Прямой порядок слов вместо вопросительного: 'Why you are late?' — неправильно, нужно 'Why are you late?'.\n— Пропуск вспомогательного глагола: 'Where you live?' — неправильно, нужно 'Where do you live?'.",
            ],
            [
                'level' => 'B2',
                'lesson' => '',
                'title' => 'Conjunctive Adverbs',
                'category' => 'Adverbs',
                'theory' => "Conjunctive adverbs (связующие наречия) соединяют идеи между предложениями, показывая логическую связь: противопоставление, следствие, добавление, время.\n\nКогда используется:\n— противопоставление: however, nevertheless, on the other hand.\n— следствие/результат: therefore, consequently, as a result.\n— добавление информации: moreover, furthermore, in addition.\n— последовательность: then, next, finally.\n— обычно отделяются запятой и часто стоят в начале предложения.\n\nСтруктура:\nПредложение 1. Conjunctive adverb, предложение 2. Или: Предложение 1; conjunctive adverb, предложение 2.\n\nПримеры:\nThe weather was terrible. However, we still enjoyed the trip. He didn't study. Therefore, he failed the exam. She's talented; moreover, she works incredibly hard. First, preheat the oven. Then, mix the ingredients.\n\nДополнительные примеры:\nThe plan failed; nevertheless, we learned a lot. Prices rose sharply; consequently, sales fell. The hotel was expensive. Furthermore, it was far from the beach. Finally, don't forget to save your work.\n\nЧастые ошибки:\n— Использование conjunctive adverb как обычного союза без правильной пунктуации: 'I was tired however I finished the work' — неправильно, нужно 'I was tired. However, I finished the work.' или 'I was tired; however, I finished the work.'\n— Путаница but и however по пунктуации: but не требует точки с запятой перед собой, however (как conjunctive adverb) обычно требует.",
            ],
            [
                'level' => 'B2',
                'lesson' => '',
                'title' => 'Viewpoint and Commenting Adverbs',
                'category' => 'Adverbs',
                'theory' => "Viewpoint and commenting adverbs показывают отношение или личную оценку говорящего ко всему высказыванию, а не описывают конкретное действие.\n\nКогда используется:\n— чтобы выразить точку зрения говорящего: Personally, I think it's a bad idea.\n— чтобы прокомментировать всю ситуацию, а не отдельное слово: Fortunately, nobody was hurt.\n— часто стоят в начале предложения и отделяются запятой.\n\nФормы:\npersonally, honestly, frankly, surprisingly, fortunately, unfortunately, obviously, clearly, apparently, luckily.\n\nПримеры:\nHonestly, I don't think this will work. Fortunately, the flight wasn't delayed. Personally, I prefer tea to coffee. Surprisingly, she agreed immediately. Obviously, we need more time. Unfortunately, the shop was closed.\n\nДополнительные примеры:\nFrankly, I was disappointed with the result. Clearly, something went wrong. Apparently, he already knew the news. Luckily, we found a parking space. Interestingly, both studies reached the same conclusion.\n\nЧастые ошибки:\n— Путаница viewpoint adverb с adverb of manner: 'She answered honestly' (как она отвечала — manner) отличается от 'Honestly, I don't know' (комментарий говорящего в начале — viewpoint).\n— Пропуск запятой после наречия в начале предложения — это не строгая ошибка, но ухудшает читаемость.",
            ],
            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Intensifiers and Mitigators',
                'category' => 'Adverbs',
                'theory' => "Intensifiers усиливают значение прилагательного, наречия или глагола; mitigators (или downtoners), наоборот, смягчают, ослабляют его.\n\nКогда используется:\n— intensifiers для усиления: very, really, so, extremely, absolutely.\n— mitigators для смягчения: quite, rather, a little, slightly, somewhat, kind of.\n— выбор между сильными и слабыми intensifiers зависит от того, является ли прилагательное 'градуируемым' (gradable: big, cold) или 'предельным' (absolute/ungradable: perfect, impossible) — с предельными используются absolutely/completely, а не very.\n\nФормы:\nIntensifiers: very, really, so, extremely, incredibly, absolutely, completely, totally. Mitigators: quite, rather, fairly, a bit, a little, slightly, somewhat.\n\nПримеры:\nThis is really important. The film was absolutely amazing. I'm quite tired today. It's a bit cold outside. She was extremely nervous before the interview. That's completely impossible.\n\nДополнительные примеры:\nHe's rather shy at first. The soup is slightly too salty. I'm somewhat confused by these instructions. That was totally unnecessary. She's fairly confident about the results.\n\nЧастые ошибки:\n— very с предельными прилагательными: 'very perfect' — неправильно, нужно 'absolutely perfect' (perfect — предельное качество, either/or, не имеет степеней).\n— so без придаточного результата в формальном стиле: 'so' часто требует продолжения that-clause в письменной речи: 'It was so cold that we went home.'",
            ],
            [
                'level' => 'C1',
                'lesson' => '',
                'title' => 'Adverbial Nouns',
                'category' => 'Adverbs',
                'theory' => "Adverbial nouns — существительные, которые в предложении выполняют функцию наречия (обычно времени, места или меры) без предлога перед собой.\n\nКогда используется:\n— для выражения времени без предлога: I'll see you next week. She called me yesterday.\n— для выражения расстояния или направления: He walked home. We went that way.\n— для выражения меры/количества: The box weighs ten kilos. It costs ten dollars.\n\nФормы:\nТипичные adverbial nouns: today, yesterday, tomorrow, home, this way, next year, last month, north/south/east/west.\n\nПримеры:\nI'm going home now. We met last summer. The store is two blocks north. This road leads nowhere. She's staying here next week. The trip took three days.\n\nДополнительные примеры:\nHe arrived home late. The meeting is next Monday. It's ten miles that way. I saw her last night. The package weighs five kilos.\n\nЧастые ошибки:\n— Лишний предлог перед adverbial noun: 'go to home' — неправильно, нужно просто 'go home' (home здесь уже наречие, предлог не нужен).\n— Путаница с обычным существительным, требующим предлога: 'I go house' — неправильно (house — обычное существительное, требует предлога: 'I go to the house'), в отличие от home, которое может быть adverbial noun.",
            ],
            [
                'level' => 'B2',
                'lesson' => '',
                'title' => 'Adverbial Clauses',
                'category' => 'Adverbs',
                'theory' => "Adverbial clause — придаточное предложение, которое выполняет функцию наречия: показывает время, причину, условие, цель, уступку или место главного действия.\n\nКогда используется:\n— время: She called me when she arrived.\n— причина: I stayed home because I was ill.\n— условие: If it rains, we'll stay inside.\n— уступка (несмотря на что-то): Although he was tired, he kept working.\n— цель: She studied hard so that she could pass the exam.\n— место: Sit wherever you like.\n\nФормы (типичные союзы):\nwhen/while/before/after/as soon as (время), because/since/as (причина), if/unless (условие), although/though/even though (уступка), so that/in order that (цель), where/wherever (место).\n\nПримеры:\nI'll call you when I get home. Although it was raining, we went for a walk. She left early because she was tired. If you study hard, you'll pass. He whispered so that no one would hear. We can meet wherever you want.\n\nДополнительные примеры:\nAs soon as the bell rang, the students left. Since you're here, let's talk. Unless you hurry, you'll miss the train. Even though he apologised, she was still angry. Wherever she goes, her dog follows.\n\nЧастые ошибки:\n— will в условном придаточном (после if): 'If it will rain, we'll stay home' — неправильно, нужно 'If it rains, we'll stay home'.\n— Отсутствие запятой, когда adverbial clause стоит в начале предложения: 'Although it was raining we went out' лучше писать с запятой: 'Although it was raining, we went out.'",
            ],

        ];
    }
}
