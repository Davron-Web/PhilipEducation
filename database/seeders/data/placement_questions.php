<?php

/**
 * Банк вопросов теста на определение уровня — 50 штук, по 8-9 на каждый
 * уровень A1-C2. Формат на уровень: список вопросов, у каждого 4 варианта
 * и индекс правильного (0-based). Уровни расположены по возрастанию
 * сложности — от базовых форм to be/Present Simple (A1) до нюансов
 * регистра и смешанных условных предложений (C2).
 */
return [
    'A1' => [
        ['q' => 'I ___ a student.', 'options' => ['am', 'is', 'are', 'be'], 'correct' => 0],
        ['q' => 'She ___ to school every day.', 'options' => ['go', 'goes', 'going', 'gone'], 'correct' => 1],
        ['q' => 'This is ___ apple.', 'options' => ['a', 'an', 'the', '—'], 'correct' => 1],
        ['q' => 'They ___ from Canada.', 'options' => ['is', 'am', 'are', 'be'], 'correct' => 2],
        ['q' => 'What ___ your name?', 'options' => ['is', 'are', 'do', 'does'], 'correct' => 0],
        ['q' => "He ___ like coffee.", 'options' => ["don't", "doesn't", "isn't", 'not'], 'correct' => 1],
        ['q' => 'There ___ two cats in the garden.', 'options' => ['is', 'are', 'be', 'am'], 'correct' => 1],
        ['q' => 'I have ___ brother.', 'options' => ['a', 'an', 'some', 'any'], 'correct' => 0],
    ],
    'A2' => [
        ['q' => 'Yesterday, we ___ to the cinema.', 'options' => ['go', 'went', 'gone', 'goes'], 'correct' => 1],
        ['q' => 'London is ___ than Paris.', 'options' => ['big', 'bigger', 'biggest', 'more big'], 'correct' => 1],
        ['q' => 'She usually ___ up at 7 am.', 'options' => ['wake', 'wakes', 'woke', 'waking'], 'correct' => 1],
        ['q' => 'I was born ___ 1998.', 'options' => ['in', 'on', 'at', 'by'], 'correct' => 0],
        ['q' => "He can't come ___ he is ill.", 'options' => ['because', 'so', 'but', 'although'], 'correct' => 0],
        ['q' => 'We ___ dinner when the phone rang.', 'options' => ['have', 'had', 'were having', 'has'], 'correct' => 2],
        ['q' => "This is the ___ film I've ever seen.", 'options' => ['bad', 'worse', 'worst', 'baddest'], 'correct' => 2],
        ['q' => 'I have ___ finished my homework.', 'options' => ['already', 'yet', 'still', 'ago'], 'correct' => 0],
    ],
    'B1' => [
        ['q' => 'I ___ never been to Japan.', 'options' => ['have', 'has', 'had', 'having'], 'correct' => 0],
        ['q' => 'If it rains, we ___ stay at home.', 'options' => ['will', 'would', 'can', 'must'], 'correct' => 0],
        ['q' => "You ___ smoke here — it's not allowed.", 'options' => ["mustn't", "don't have to", "needn't", 'can'], 'correct' => 0],
        ['q' => 'She has lived here ___ 2015.', 'options' => ['for', 'since', 'from', 'during'], 'correct' => 1],
        ['q' => "Could you turn ___ the music? It's too loud.", 'options' => ['down', 'up', 'off', 'on'], 'correct' => 0],
        ['q' => 'By the time I arrived, the meeting ___ already started.', 'options' => ['has', 'had', 'was', 'is'], 'correct' => 1],
        ['q' => 'He asked me where ___.', 'options' => ['I live', 'do I live', 'did I live', 'I lived'], 'correct' => 3],
        ['q' => "I'm looking forward to ___ you.", 'options' => ['see', 'seeing', 'saw', 'seen'], 'correct' => 1],
        ['q' => 'English ___ in many countries.', 'options' => ['speaks', 'is spoken', 'spoke', 'speaking'], 'correct' => 1],
    ],
    'B2' => [
        ['q' => 'If I ___ you, I would apologize.', 'options' => ['am', 'was', 'were', 'be'], 'correct' => 2],
        ['q' => 'The report ___ by the manager tomorrow.', 'options' => ['will write', 'will be written', 'is writing', 'written'], 'correct' => 1],
        ['q' => 'She said she ___ me the next day.', 'options' => ['will call', 'would call', 'calls', 'called'], 'correct' => 1],
        ['q' => 'The man ___ car was stolen called the police.', 'options' => ['who', 'which', 'whose', 'that'], 'correct' => 2],
        ['q' => 'Despite ___ hard, he failed the exam.', 'options' => ['study', 'studying', 'studied', 'to study'], 'correct' => 1],
        ['q' => 'I wish I ___ more time to relax.', 'options' => ['have', 'had', 'has', 'having'], 'correct' => 1],
        ['q' => 'He denied ___ the money.', 'options' => ['steal', 'to steal', 'stealing', 'stole'], 'correct' => 2],
        ['q' => "It's high time you ___ your homework.", 'options' => ['do', 'did', 'done', 'doing'], 'correct' => 1],
        ['q' => 'The bridge, ___ was built in 1990, is now closed.', 'options' => ['that', 'which', 'who', 'whose'], 'correct' => 1],
    ],
    'C1' => [
        ['q' => 'If she had studied harder, she ___ the exam.', 'options' => ['would pass', 'would have passed', 'will pass', 'passed'], 'correct' => 1],
        ['q' => 'Never ___ such a beautiful sunset.', 'options' => ['I saw', 'have I seen', 'I have seen', 'did I saw'], 'correct' => 1],
        ['q' => 'He is said ___ a fortune in the stock market.', 'options' => ['to make', 'to have made', 'making', 'made'], 'correct' => 1],
        ['q' => 'No sooner ___ home than the phone rang.', 'options' => ['I had arrived', 'had I arrived', 'I arrived', 'did I arrive'], 'correct' => 1],
        ['q' => "It's about time this problem ___.", 'options' => ['address', 'addressed', 'was addressed', 'is addressing'], 'correct' => 2],
        ['q' => 'Had I known about the traffic, I ___ earlier.', 'options' => ['would leave', 'would have left', 'left', 'leave'], 'correct' => 1],
        ['q' => 'The negotiations broke ___ without reaching an agreement.', 'options' => ['down', 'off', 'up', 'out'], 'correct' => 0],
        ['q' => 'Rarely ___ such dedication in an employee.', 'options' => ['we see', 'do we see', 'we saw', 'did we see'], 'correct' => 1],
    ],
    'C2' => [
        ['q' => 'Were it not ___ your help, I would have failed.', 'options' => ['for', 'to', 'of', 'with'], 'correct' => 0],
        ['q' => "She has an uncanny ability to ___ people's thoughts.", 'options' => ['read', 'reading', 'reads', 'to read'], 'correct' => 0],
        ['q' => "The committee's decision was, to all intents and ___, final.", 'options' => ['purposes', 'reasons', 'meanings', 'aims'], 'correct' => 0],
        ['q' => "He's not as green as he's cabbage-looking — meaning he's actually ___.", 'options' => ['naive', 'shrewd', 'foolish', 'young'], 'correct' => 1],
        ['q' => 'Little ___ that the deal would fall through.', 'options' => ['did they know', 'they knew', 'they did know', 'knew they'], 'correct' => 0],
        ['q' => 'If the vote ___ tied, the chairman would have the casting vote.', 'options' => ['were to be', 'was', 'is', 'be'], 'correct' => 0],
        ['q' => "The minister's speech was a masterclass in ___ ambiguity.", 'options' => ['calculated', 'calculating', 'calculate', 'calculation'], 'correct' => 0],
        ['q' => 'One cannot help but ___ the irony of the situation.', 'options' => ['admire', 'admiring', 'to admire', 'admired'], 'correct' => 0],
    ],
];
