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
                'level_id' => $levelId,
                'theory' => $data['theory'],
            ]);

            Lesson::where('level_id', $levelId)
                ->where('title', $data['lesson'])
                ->update(['grammar_topic_id' => $topic->id]);
        }
    }

    /**
     * @return array<int, array{level: string, lesson: string, title: string, theory: string}>
     */
    private function topics(): array
    {
        return [
            // A1
            [
                'level' => 'A1',
                'lesson' => 'Present Simple Basics',
                'title' => 'Present Simple',
                'theory' => "Present Simple используется для описания привычных действий, фактов и повторяющихся событий.\n\n"
                    ."Утверждение: I/You/We/They + глагол; He/She/It + глагол + -s.\n"
                    ."Примеры: I work in an office. She works in an office.\n\n"
                    ."Отрицание: do/does + not + глагол.\n"
                    ."Примеры: I do not (don't) like coffee. He does not (doesn't) like coffee.\n\n"
                    ."Вопрос: Do/Does + подлежащее + глагол?\n"
                    ."Пример: Do you speak English? Does she speak English?\n\n"
                    ."Present Simple часто используется со словами-маркерами: always, usually, often, sometimes, never, every day.\n"
                    .'Пример: I always wake up at 7 a.m. We never eat meat.',
            ],
            [
                'level' => 'A1',
                'lesson' => 'Verb To Be: Am, Is, Are',
                'title' => 'Глагол to be (am/is/are)',
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
                'theory' => "Present Continuous описывает действие, которое происходит прямо сейчас, в момент речи, или временную ситуацию.\n\n"
                    ."Формула: am/is/are + глагол + -ing.\n"
                    ."Примеры: I am reading a book. She is cooking dinner. They are playing football.\n\n"
                    ."Отрицание: am/is/are + not + глагол-ing.\n"
                    ."Пример: I am not watching TV now.\n\n"
                    ."Вопрос: Am/Is/Are + подлежащее + глагол-ing?\n"
                    ."Пример: Are you listening to me?\n\n"
                    ."Слова-маркеры: now, right now, at the moment, look!, listen!\n"
                    .'Сравните: I read books every day (привычка, Present Simple) — I am reading a book now (сейчас, Present Continuous).',
            ],
            [
                'level' => 'A1',
                'lesson' => 'Past Simple Basics',
                'title' => 'Past Simple',
                'theory' => "Past Simple используется для завершённых действий в прошлом, которые произошли в конкретный момент времени.\n\n"
                    ."Правильные глаголы: глагол + -ed. Пример: work → worked, play → played.\n"
                    ."Неправильные глаголы имеют особую форму: go → went, see → saw, be → was/were.\n\n"
                    ."Утверждение: I worked yesterday. She went to Paris last year.\n"
                    ."Отрицание: did + not (didn't) + глагол в начальной форме.\n"
                    ."Пример: I didn't work yesterday. She didn't go anywhere.\n"
                    ."Вопрос: Did + подлежащее + глагол в начальной форме?\n"
                    ."Пример: Did you see him?\n\n"
                    .'Слова-маркеры: yesterday, last week/month/year, in 2020, ago (two days ago).',
            ],
            [
                'level' => 'A1',
                'lesson' => 'Articles: A, An, The',
                'title' => 'Артикли a/an/the',
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
                'theory' => "Past Continuous описывает действие, которое происходило в определённый момент в прошлом, часто на фоне которого случилось другое, более короткое действие.\n\n"
                    ."Формула: was/were + глагол + -ing.\n"
                    ."Примеры: I was reading at 8 p.m. They were playing football when it started to rain.\n\n"
                    ."Часто используется вместе с Past Simple: while/when.\n"
                    ."Пример: While I was cooking, the phone rang. (длительное действие + короткое действие)\n\n"
                    ."Отрицание: was/were + not.\n"
                    ."Пример: She wasn't sleeping at midnight.\n"
                    ."Вопрос: Was/Were + подлежащее + глагол-ing?\n"
                    .'Пример: What were you doing at 5 p.m. yesterday?',
            ],
            [
                'level' => 'A2',
                'lesson' => 'Future Simple: Will',
                'title' => 'Future Simple (will)',
                'theory' => "Future Simple с will используется для спонтанных решений, обещаний, предположений о будущем и предложений помощи.\n\n"
                    ."Формула: will + глагол в начальной форме (для всех лиц).\n"
                    ."Примеры: I will (I'll) call you tomorrow. It will rain later, I think.\n\n"
                    ."Отрицание: will not (won't) + глагол.\n"
                    ."Пример: She won't be late.\n"
                    ."Вопрос: Will + подлежащее + глагол?\n"
                    ."Пример: Will you help me, please?\n\n"
                    ."Will часто используется со словами: probably, I think, I'm sure, tomorrow, next week.\n"
                    ."Сравните с going to: will — решение в момент речи ('The bag is heavy, I'll carry it'), going to — заранее спланированное намерение ('I'm going to visit my parents this weekend').",
            ],
            [
                'level' => 'A2',
                'lesson' => 'Present Perfect: Introduction',
                'title' => 'Present Perfect (введение)',
                'theory' => "Present Perfect связывает прошлое с настоящим: действие произошло в неопределённом прошлом, но результат важен сейчас.\n\n"
                    ."Формула: have/has + глагол в 3-й форме (Participle II).\n"
                    ."Правильные глаголы: + -ed (worked). Неправильные: особая форма (go → gone, see → seen).\n\n"
                    ."Примеры: I have finished my homework. She has already left.\n"
                    ."Отрицание: have/has + not (haven't/hasn't).\n"
                    ."Пример: I haven't seen this film.\n"
                    ."Вопрос: Have/Has + подлежащее + глагол в 3-й форме?\n"
                    ."Пример: Have you ever been to Japan?\n\n"
                    ."Слова-маркеры: already, just, yet, ever, never, so far, since, for.\n"
                    .'Важно не путать с Past Simple: Present Perfect — без указания конкретного времени, Past Simple — с конкретным временем (yesterday, in 2020).',
            ],
            [
                'level' => 'A2',
                'lesson' => 'First Conditional',
                'title' => 'First Conditional (реальное условие)',
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
                'theory' => "Hedging — использование смягчающих конструкций, чтобы высказывание звучало менее категорично, более вежливо или осторожно. Особенно важно в академическом и деловом английском.\n\n"
                    ."Модальные глаголы: may, might, could, would suggest.\n"
                    ."Пример: This might explain the results. The data would suggest a correlation.\n\n"
                    ."Вводные фразы: it seems that, it appears that, it could be argued that, arguably.\n"
                    ."Пример: It appears that the policy has had a limited effect.\n\n"
                    ."Наречия степени: relatively, somewhat, to some extent, largely.\n"
                    ."Пример: The results were somewhat inconclusive.\n\n"
                    .'Hedging позволяет избежать излишне категоричных утверждений (avoid, prevent absolute claims), что особенно важно в научных работах, где выводы часто носят вероятностный характер, а также в деловой переписке для сохранения вежливого тона.',
            ],
        ];
    }
}
