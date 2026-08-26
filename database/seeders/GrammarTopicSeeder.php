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
                'category' => 'Tenses',
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
                'category' => 'Tenses',
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
                'category' => 'Tenses',
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
                'theory' => "Местоимения (pronouns) заменяют существительные, чтобы избежать повторов.\n\n"
                    ."Личные местоимения (subject): I, you, he, she, it, we, they — используются как подлежащее.\n"
                    ."Пример: She works in a bank. They live in London.\n\n"
                    ."Объектные местоимения (object): me, you, him, her, it, us, them — используются как дополнение, после глагола или предлога.\n"
                    ."Пример: Can you help me? I gave the book to her.\n\n"
                    ."Притяжательные местоимения (possessive): mine, yours, his, hers, its, ours, theirs — заменяют «существительное + притяжательное прилагательное».\n"
                    ."Пример: This is my book. This book is mine.\n\n"
                    ."Возвратные местоимения (reflexive): myself, yourself, himself, herself, itself, ourselves, yourselves, themselves — когда подлежащее и дополнение совпадают.\n"
                    ."Пример: I cut myself while cooking. She looked at herself in the mirror.\n\n"
                    ."Важно: it используется для предметов, животных (если пол неизвестен) и в безличных конструкциях о погоде и времени: It's raining. It's 5 o'clock.",
            ],
            [
                'level' => 'A1',
                'lesson' => 'Plural Nouns',
                'title' => 'Существительные (Nouns)',
                'category' => 'Nouns',
                'theory' => "Существительные (nouns) называют людей, предметы, места и понятия. В английском они бывают исчисляемые (countable) и неисчисляемые (uncountable).\n\n"
                    ."Множественное число исчисляемых существительных обычно образуется добавлением -s: book → books, car → cars.\n\n"
                    ."После шипящих (-s, -ss, -sh, -ch, -x, -z) добавляется -es: box → boxes, watch → watches, bus → buses.\n\n"
                    ."Если существительное оканчивается на согласную + y, y меняется на i + es: city → cities, baby → babies. Если перед y стоит гласная — просто -s: boy → boys, day → days.\n\n"
                    ."Неправильные формы множественного числа: man → men, woman → women, child → children, tooth → teeth, foot → feet, person → people, mouse → mice.\n\n"
                    ."Неисчисляемые существительные (water, information, advice, furniture, money) не имеют множественного числа и не используются с a/an; для счёта используются слова-счётчики: a piece of advice, a glass of water.",
            ],
            [
                'level' => 'A2',
                'lesson' => 'Adverbs of Frequency',
                'title' => 'Наречия (Adverbs)',
                'category' => 'Adverbs',
                'theory' => "Наречия (adverbs) описывают, как, когда, где или как часто происходит действие, и обычно относятся к глаголу, прилагательному или другому наречию.\n\n"
                    ."Наречия образа действия чаще всего образуются от прилагательного + -ly: quick → quickly, careful → carefully, happy → happily (y меняется на i).\n"
                    ."Исключения: good → well, fast → fast, hard → hard.\n\n"
                    ."Наречия частотности (adverbs of frequency) показывают, как часто происходит действие: always, usually, often, sometimes, rarely, seldom, never. Ставятся перед смысловым глаголом, но после глагола to be.\n"
                    ."Примеры: I always drink coffee in the morning. She is never late.\n\n"
                    ."Место наречий образа действия — обычно после глагола или дополнения: She sings beautifully. He drives carefully.\n\n"
                    ."Наречия степени (very, quite, really, too, enough) усиливают или ослабляют значение прилагательного или другого наречия: The exam was really difficult. She speaks English quite fluently.",
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
                'theory' => "Personal pronouns (личные местоимения) — это полная система форм для каждого лица и числа, заменяющая имена людей и предметов.\n\n"
                    ."Единственное число: I (я), you (ты), he (он), she (она), it (оно/это).\n"
                    ."Множественное число: we (мы), you (вы), they (они).\n\n"
                    ."У каждого личного местоимения есть форма подлежащего (I, he, she) и форма дополнения (me, him, her) — они рассматриваются отдельно в темах Subject Pronouns и Object Pronouns.\n\n"
                    ."It используется не только для предметов и животных, но и в безличных предложениях: It's cold today. It's 6 p.m.\n\n"
                    ."В современном английском they всё чаще используется как местоимение единственного числа, когда пол человека неизвестен или не указывается: Someone left their umbrella here.",
            ],
            [
                'level' => 'A1',
                'lesson' => '',
                'title' => 'Subject Pronouns',
                'category' => 'Pronouns',
                'theory' => "Subject pronouns (местоимения-подлежащие) — I, you, he, she, it, we, they — стоят перед глаголом и показывают, кто выполняет действие.\n\n"
                    ."Примеры: I live in Tashkent. She works at a hospital. They study English together.\n\n"
                    ."Глагол в Present Simple согласуется с местоимением: he/she/it + глагол с окончанием -s (he works), остальные — без -s (I work, they work).\n\n"
                    ."Частая ошибка носителей других языков — использовать объектное местоимение вместо подлежащего: 'Me and him went to the party' неправильно; правильно 'He and I went to the party'.\n\n"
                    ."В сложном подлежащем (два человека) вежливая норма — ставить 'I' на последнее место: My friend and I, not I and my friend.",
            ],
            [
                'level' => 'A1',
                'lesson' => '',
                'title' => 'Object Pronouns',
                'category' => 'Pronouns',
                'theory' => "Object pronouns (объектные местоимения) — me, you, him, her, it, us, them — используются как прямое или косвенное дополнение, то есть отвечают на вопрос «кого? что? кому?».\n\n"
                    ."После глагола (прямое дополнение): I saw her yesterday. Call me later.\n\n"
                    ."После глагола с двумя дополнениями (косвенное + прямое): She gave him a present. Или с предлогом to/for: She gave a present to him.\n\n"
                    ."После предлогов: This letter is for you. Come with us.\n\n"
                    ."В неформальной речи объектная форма часто используется и в кратких ответах: 'Who wants coffee?' — 'Me!' (вместо более формального 'I do').",
            ],
            [
                'level' => 'A2',
                'lesson' => '',
                'title' => 'Reflexive Pronouns',
                'category' => 'Pronouns',
                'theory' => "Reflexive pronouns (возвратные местоимения) — myself, yourself, himself, herself, itself, oneself, ourselves, yourselves, themselves — используются, когда подлежащее и дополнение обозначают одно и то же лицо.\n\n"
                    ."Пример: I hurt myself. She taught herself to play the piano. They introduced themselves.\n\n"
                    ."Устойчивые выражения: enjoy yourself (хорошо провести время), help yourself (угощайся), by myself/on my own (в одиночку, самостоятельно).\n\n"
                    ."Некоторые глаголы, требующие возвратного местоимения в других языках, в английском обычно обходятся без него: I washed (not 'I washed myself', если речь просто о гигиене), She feels good (not 'She feels herself good').\n\n"
                    ."Не путайте с emphatic pronouns — та же форма, но другая функция (см. тему Emphatic Pronouns).",
            ],
            [
                'level' => 'A2',
                'lesson' => '',
                'title' => 'Demonstrative Pronouns',
                'category' => 'Pronouns',
                'theory' => "Demonstrative pronouns (указательные местоимения) — this, that, these, those — указывают на конкретный предмет или предметы.\n\n"
                    ."This (это/этот) и these (эти) — для близких по расстоянию или времени предметов: This is my bag. These are my keys.\n\n"
                    ."That (то/тот) и those (те) — для далёких предметов: That was a great film. Those shoes look expensive.\n\n"
                    ."This/that — единственное число, these/those — множественное. Глагол согласуется соответственно: This is..., These are....\n\n"
                    ."Отличие от указательных прилагательных (determiners): в 'This book is mine' this — определитель перед существительным, а в 'This is mine' this — самостоятельное местоимение, заменяющее существительное.",
            ],
            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Emphatic Pronouns',
                'category' => 'Pronouns',
                'theory' => "Emphatic pronouns (эмфатические местоимения) используют те же формы, что и reflexive pronouns (myself, himself, themselves...), но не являются дополнением — они лишь усиливают подлежащее или дополнение, подчёркивая «сам, лично».\n\n"
                    ."Пример: I myself don't believe it. (= I personally don't believe it.) The manager himself called me.\n\n"
                    ."Могут стоять сразу после существительного/местоимения или в конце предложения без изменения смысла: She repaired the car herself. / She herself repaired the car.\n\n"
                    ."Ключевое отличие от reflexive pronouns: эмфатическое местоимение можно убрать из предложения без потери грамматической правильности — оно не является членом предложения (дополнением), а лишь усилением.\n\n"
                    ."Сравните: She hurt herself (reflexive, обязательное дополнение) — She herself opened the door (emphatic, можно убрать: She opened the door).",
            ],
            [
                'level' => 'A2',
                'lesson' => '',
                'title' => 'Interrogative Pronouns',
                'category' => 'Pronouns',
                'theory' => "Interrogative pronouns (вопросительные местоимения) — who, whom, whose, what, which — используются для формирования вопросов.\n\n"
                    ."Who — вопрос о подлежащем-человеке: Who called you? Whom — вопрос о дополнении-человеке, формальный стиль: Whom did you meet? (в разговорной речи обычно заменяется на who).\n\n"
                    ."Whose — вопрос о принадлежности: Whose is this jacket? Whose bag is this? (второй вариант — уже определитель, а не местоимение).\n\n"
                    ."What — открытый вопрос без ограничения вариантов: What do you want to eat? Which — вопрос с ограниченным выбором: Which do you prefer, tea or coffee?\n\n"
                    ."Все эти слова также используются как relative pronouns в придаточных предложениях — сравните с темой Relative Pronouns.",
            ],
            [
                'level' => 'A2',
                'lesson' => '',
                'title' => 'Possessive Pronouns',
                'category' => 'Pronouns',
                'theory' => "Possessive pronouns (притяжательные местоимения) — mine, yours, his, hers, its, ours, theirs — заменяют «притяжательное прилагательное + существительное» и употребляются самостоятельно, без следующего за ними существительного.\n\n"
                    ."Пример: This is my book. → This book is mine. Is this your pen? → Is this pen yours?\n\n"
                    ."Не путайте с possessive adjectives (my, your, his, her, its, our, their), которые всегда стоят перед существительным: my book (adjective) vs This book is mine (pronoun).\n\n"
                    ."Its практически не используется как самостоятельное местоимение (its употребляется только как определитель: its colour), поскольку неодушевлённые предметы редко участвуют в конструкциях типа 'this is its'.\n\n"
                    ."Устойчивая конструкция 'a friend of mine' (один из моих друзей) использует possessive pronoun после предлога of, а не possessive adjective.",
            ],
            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Indefinite Pronouns',
                'category' => 'Pronouns',
                'theory' => "Indefinite pronouns (неопределённые местоимения) обозначают неопределённых или неуказанных людей, предметы и количества: somebody/someone, anybody/anyone, nobody/no one, everybody/everyone; something, anything, nothing, everything; some, any, none, all, both, several, few, many.\n\n"
                    ."Some- используется в утвердительных предложениях: I saw someone in the garden. Any- — в вопросах и отрицаниях: Did you see anyone? I didn't see anyone.\n\n"
                    ."No- уже содержит отрицание, поэтому глагол ставится в утвердительной форме: Nobody knows the answer (не 'Nobody doesn't know').\n\n"
                    ."Местоимения на -body/-one/-thing согласуются с глаголом в единственном числе: Everybody is here. Everyone knows that.\n\n"
                    ."All, both, none, several, few, many могут заменять исчисляемые существительные во множественном числе: Many of them agreed. Few understood the question.",
            ],
            [
                'level' => 'B2',
                'lesson' => '',
                'title' => 'Dummy Pronouns',
                'category' => 'Pronouns',
                'theory' => "Dummy pronouns (местоимения-«пустышки», формальные подлежащие) — it и there — заполняют обязательную позицию подлежащего в английском предложении, не указывая на конкретный предмет.\n\n"
                    ."It используется для погоды, времени, расстояния и общих оценок: It's raining. It's 9 o'clock. It's 200 km to the city. It's important to be honest.\n\n"
                    ."There используется, чтобы сообщить о существовании чего-либо (there is/are): There is a café on the corner. There were many people at the concert.\n\n"
                    ."Отличие: it не имеет реального значения и просто занимает место подлежащего; there хотя бы указывает на существование объекта, но само по себе не является ни подлежащим по смыслу, ни обстоятельством места (не путать с there — наречием места: Put it there).\n\n"
                    ."Английский язык, в отличие от русского, не допускает предложений без подлежащего, поэтому dummy pronouns грамматически обязательны там, где в русском подлежащее просто опускается ('Идёт дождь' → It is raining).",
            ],
            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Reciprocal Pronouns',
                'category' => 'Pronouns',
                'theory' => "Reciprocal pronouns (взаимные местоимения) — each other и one another — показывают, что действие взаимно направлено между двумя и более участниками.\n\n"
                    ."Пример: Tom and Jerry always argue with each other. The students helped one another during the exam.\n\n"
                    ."Традиционно each other используется для двух человек, а one another — для трёх и более, но в современном английском это различие практически стёрлось, и оба варианта взаимозаменяемы.\n\n"
                    ."Притяжательная форма: each other's, one another's — We celebrated each other's birthdays.\n\n"
                    ."Не путайте с reflexive pronouns: 'They blamed themselves' (каждый винил себя самого) отличается по смыслу от 'They blamed each other' (они винили друг друга).",
            ],
            [
                'level' => 'B1',
                'lesson' => '',
                'title' => 'Relative Pronouns',
                'category' => 'Pronouns',
                'theory' => "Relative pronouns (относительные местоимения) — who, whom, whose, which, that — вводят придаточное предложение и одновременно заменяют существительное, о котором идёт речь.\n\n"
                    ."Who — для людей в роли подлежащего: The woman who called you is my aunt. Whom — для людей в роли дополнения (формально): The man whom I met was very kind.\n\n"
                    ."Which — для предметов и животных: The car which I bought is red. That — для людей и предметов в defining (уточняющих) придаточных, более разговорный вариант: The book that I read was boring.\n\n"
                    ."Whose — притяжательное значение, для людей и предметов: The boy whose bike was stolen called the police.\n\n"
                    ."Подробнее о построении самих придаточных предложений с этими местоимениями — в теме Relative Clauses (категория Phrases and Clauses).",
            ],
            [
                'level' => 'B2',
                'lesson' => '',
                'title' => 'Impersonal Pronouns',
                'category' => 'Pronouns',
                'theory' => "Impersonal pronouns (безличные местоимения в обобщающем значении) — one, generic you, generic they — используются, чтобы говорить о людях в целом, а не о конкретном человеке.\n\n"
                    ."One — формальный, часто используется в академическом и официальном стиле: One should always tell the truth. One must follow the rules.\n\n"
                    ."Generic you — неформальный аналог one, гораздо более распространён в разговорной речи: You can't please everyone. You never know what will happen.\n\n"
                    ."Generic they — используется, когда речь идёт о неопределённой группе людей, часто в значении «говорят, что»: They say it's going to rain. In Japan, they drive on the left.\n\n"
                    ."Выбор между one/you/they зависит от регистра речи: one — самый формальный и книжный вариант, в повседневной речи звучит несколько чопорно.",
            ],
            [
                'level' => 'C1',
                'lesson' => '',
                'title' => 'Nominal Relative Pronouns',
                'category' => 'Pronouns',
                'theory' => "Nominal relative pronouns (номинативные относительные местоимения) — what, whoever, whatever, whichever, whomever — одновременно выполняют роль относительного местоимения и его антецедента (существительного, к которому оно относится), образуя целое именное придаточное предложение.\n\n"
                    ."What = 'the thing(s) that': What she said surprised everyone. (= The thing that she said...) I don't understand what you mean.\n\n"
                    ."Whoever = 'the person who / anyone who': Whoever wins the race gets a prize. Whatever = 'anything that': You can do whatever you like.\n\n"
                    ."Whichever выбирает из ограниченного набора вариантов: Choose whichever option suits you best.\n\n"
                    ."Отличие от обычных relative pronouns: обычные (who, which, that) всегда ссылаются на уже названное существительное в главном предложении, а nominal relative pronouns сами образуют подлежащее или дополнение всего предложения — отдельного антецедента не требуется.",
            ],
            [
                'level' => 'C1',
                'lesson' => '',
                'title' => 'Archaic Pronouns',
                'category' => 'Pronouns',
                'theory' => "Archaic pronouns (архаичные местоимения) — thou, thee, thy, thine, ye — исторические формы 2-го лица, вышедшие из повседневного употребления к XVIII веку, но встречающиеся в поэзии, Шекспире, Библии короля Якова (King James Bible) и стилизованных текстах.\n\n"
                    ."Thou — подлежащее, единственное число (= you, ты): Thou art welcome. (= You are welcome.)\n"
                    ."Thee — дополнение, единственное число (= you): I give thee my word.\n\n"
                    ."Thy/thine — притяжательные формы (= your/yours): thy kingdom come; thine eyes (thine перед гласным звуком, как an перед гласной).\n\n"
                    ."Ye — форма множественного числа для подлежащего (= you, вы): Hear ye, hear ye!\n\n"
                    ."Сегодня эти формы практически не используются в живой речи, но узнавание их важно для понимания классической литературы, религиозных текстов и некоторых устойчивых архаизмов (например, 'the powers that be').",
            ],
            [
                'level' => 'B2',
                'lesson' => '',
                'title' => 'Distributive Pronouns',
                'category' => 'Pronouns',
                'theory' => "Distributive pronouns (дистрибутивные местоимения) — each, either, neither — указывают на членов группы по отдельности, а не на группу как целое.\n\n"
                    ."Each — каждый из группы (два и более): Each of the students has a laptop. Each costs \$10.\n\n"
                    ."Either — один из двух (в утвердительном или вопросительном смысле): You can take either of the two roads. Either is fine with me.\n\n"
                    ."Neither — ни один из двух (отрицательное значение): Neither of the answers is correct.\n\n"
                    ."Важно: each, either, neither согласуются с глаголом в единственном числе, даже когда после них стоит 'of + существительное во множественном числе': Each of them has a key (не 'have').",
            ],
        ];
    }
}
