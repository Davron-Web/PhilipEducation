<?php

/**
 * 20 записей IELTS Listening для IeltsBatch3Seeder.
 *
 * Пять на каждый уровень, по четырём частям экзамена: бытовой диалог,
 * монолог-объявление, учебный разговор и академическая лекция. Реплики
 * подписаны именами — расшифровку читает speechSynthesis, и без подписей
 * ученик не слышит, где меняется говорящий.
 */

return [
    // ─── A2: бытовые диалоги (Section 1) ─────────────────────────────────
    [
        'title' => 'Ordering a Taxi',
        'level' => 'A2',
        'text' => "Operator: City Cabs, good evening.\n"
            ."Caller: Hello, I'd like to book a taxi for tomorrow morning, please.\n"
            ."Operator: Certainly. What time do you need it?\n"
            ."Caller: Quarter past six, if that's possible. I have a flight at nine.\n"
            ."Operator: That's fine. Where are we picking you up from?\n"
            ."Caller: Forty-two Ashfield Road, the block of flats opposite the pharmacy.\n"
            ."Operator: And where are you going?\n"
            ."Caller: The airport, terminal two.\n"
            ."Operator: How many passengers?\n"
            ."Caller: Two adults and one large suitcase each.\n"
            ."Operator: Then I'll send an estate car rather than a saloon. The fare to terminal two is thirty-eight pounds, and there's no extra charge for the early hour before seven.\n"
            ."Caller: Can I pay by card in the car?\n"
            ."Operator: Yes, all our drivers take cards. Could I take a name and a mobile number?\n"
            ."Caller: Helen Marsh, and the number is 07845 221 390.\n"
            ."Operator: Thank you, Ms Marsh. The driver will text you when he arrives and will wait ten minutes at no charge.",
        'questions' => [
            ['q' => 'What time does the caller want the taxi?', 'options' => ['6:00', '6:15', '6:50', '9:00'], 'correct' => 1],
            ['q' => 'Why does the operator send an estate car?', 'options' => ['It is cheaper', 'Because of the luggage', 'No saloons are available', 'The passenger asked for one'], 'correct' => 1],
            ['q' => 'How much is the fare?', 'options' => ['28 pounds', '32 pounds', '38 pounds', '48 pounds'], 'correct' => 2],
            ['q' => 'What is said about the early hour?', 'options' => ['It costs extra', 'It costs nothing extra', 'It doubles the fare', 'It is not possible'], 'correct' => 1],
            ['q' => 'How long will the driver wait free of charge?', 'options' => ['Five minutes', 'Ten minutes', 'Fifteen minutes', 'Twenty minutes'], 'correct' => 1],
        ],
    ],
    [
        'title' => 'Joining a Gym',
        'level' => 'A2',
        'text' => "Receptionist: Good afternoon, welcome to Riverside Fitness. How can I help?\n"
            ."Visitor: Hi, I'd like to ask about membership.\n"
            ."Receptionist: Of course. We have three options. The standard membership is thirty pounds a month and gives you the gym and the changing rooms. The plus membership is forty-five and adds all the classes. The full membership is sixty and includes the swimming pool.\n"
            ."Visitor: What sort of classes are there?\n"
            ."Receptionist: Yoga, spinning, boxing and aqua aerobics. Most classes are in the evening, but yoga also runs at seven in the morning on Tuesdays and Thursdays.\n"
            ."Visitor: I think the plus membership would suit me. Is there a joining fee?\n"
            ."Receptionist: There's normally a twenty-pound joining fee, but we're waiving it this month.\n"
            ."Visitor: And can I cancel at any time?\n"
            ."Receptionist: You need to give one month's notice in writing. There's no minimum contract beyond that.\n"
            ."Visitor: One more thing — what are the opening hours?\n"
            ."Receptionist: Six in the morning until ten at night on weekdays, and eight until eight at weekends.",
        'questions' => [
            ['q' => 'How much is the plus membership per month?', 'options' => ['30 pounds', '45 pounds', '60 pounds', '20 pounds'], 'correct' => 1],
            ['q' => 'What does the full membership add?', 'options' => ['The classes', 'The changing rooms', 'The swimming pool', 'Personal training'], 'correct' => 2],
            ['q' => 'When does the morning yoga class run?', 'options' => ['Every day', 'Tuesdays and Thursdays', 'Weekends only', 'Mondays and Fridays'], 'correct' => 1],
            ['q' => 'What is said about the joining fee?', 'options' => ['It is twenty pounds', 'It has been waived this month', 'It is added to the first payment', 'There has never been one'], 'correct' => 1],
            ['q' => 'How much notice is needed to cancel?', 'options' => ['One week', 'Two weeks', 'One month', 'Three months'], 'correct' => 2],
        ],
    ],
    [
        'title' => 'Enquiring About a Language Course',
        'level' => 'A2',
        'text' => "Adviser: Good morning, Meadow Language School.\n"
            ."Student: Hello. I'd like some information about your Spanish courses.\n"
            ."Adviser: Certainly. Are you a complete beginner?\n"
            ."Student: I studied a little at school, but that was ten years ago.\n"
            ."Adviser: Then I'd suggest the elementary group rather than the beginners. It meets twice a week, Mondays and Wednesdays, from half past six to eight.\n"
            ."Student: How many people are in a group?\n"
            ."Adviser: Never more than nine. The course runs for twelve weeks and costs two hundred and seventy pounds, including the textbook.\n"
            ."Student: Is there a test before I start?\n"
            ."Adviser: There's a short placement test online, about twenty minutes. It's free, and it just helps us put you in the right group.\n"
            ."Student: When does the next course begin?\n"
            ."Adviser: The fourteenth of October. Places usually go by the end of September, so I'd book soon.\n"
            ."Student: And if I miss a lesson?\n"
            ."Adviser: We record every class and send the recording to the group, so you can catch up.",
        'questions' => [
            ['q' => 'Which group does the adviser recommend?', 'options' => ['Beginners', 'Elementary', 'Intermediate', 'Advanced'], 'correct' => 1],
            ['q' => 'How many people are in a group at most?', 'options' => ['Six', 'Nine', 'Twelve', 'Fifteen'], 'correct' => 1],
            ['q' => 'What is included in the price?', 'options' => ['A dictionary', 'The textbook', 'Private lessons', 'An exam fee'], 'correct' => 1],
            ['q' => 'How long does the placement test take?', 'options' => ['Ten minutes', 'Twenty minutes', 'Forty minutes', 'One hour'], 'correct' => 1],
            ['q' => 'What happens if a student misses a lesson?', 'options' => ['They repeat the course', 'They get a recording', 'They pay a fee', 'Nothing can be done'], 'correct' => 1],
        ],
    ],
    [
        'title' => 'Renting a Bicycle',
        'level' => 'A2',
        'text' => "Assistant: Hello, Green Wheels Bike Hire.\n"
            ."Customer: Hi, I'd like to hire a bike for a couple of days.\n"
            ."Assistant: No problem. We have city bikes, mountain bikes and electric bikes. The city bike is twelve pounds a day, the mountain bike eighteen, and the electric one twenty-five.\n"
            ."Customer: I want to ride along the river path, mostly flat.\n"
            ."Assistant: Then a city bike is plenty. Two days would be twenty-four pounds, but we do a weekend rate of twenty for Saturday and Sunday together.\n"
            ."Customer: I'll take the weekend rate then. Is a helmet included?\n"
            ."Assistant: Helmets and locks are free. A repair kit is two pounds extra, which I'd recommend for the river path — there are thorns along parts of it.\n"
            ."Customer: All right, I'll take one. What about a deposit?\n"
            ."Assistant: Fifty pounds, refunded when you bring the bike back. We need to see some photo identification too.\n"
            ."Customer: What time do you close on Sunday?\n"
            ."Assistant: Six o'clock. If you're going to be late, phone us and we'll arrange something.",
        'questions' => [
            ['q' => 'Which bike does the assistant recommend?', 'options' => ['City bike', 'Mountain bike', 'Electric bike', 'Folding bike'], 'correct' => 0],
            ['q' => 'How much is the weekend rate?', 'options' => ['12 pounds', '20 pounds', '24 pounds', '25 pounds'], 'correct' => 1],
            ['q' => 'What costs extra?', 'options' => ['The helmet', 'The lock', 'The repair kit', 'The deposit'], 'correct' => 2],
            ['q' => 'Why is the repair kit recommended?', 'options' => ['The bikes are old', 'There are thorns on the path', 'The shop closes early', 'It is required by law'], 'correct' => 1],
            ['q' => 'What must the customer show?', 'options' => ['A bank statement', 'Photo identification', 'A cycling licence', 'Proof of address'], 'correct' => 1],
        ],
    ],
    [
        'title' => 'Changing a Restaurant Booking',
        'level' => 'A2',
        'text' => "Restaurant: Good afternoon, The Old Mill.\n"
            ."Caller: Hello, I have a booking for Friday and I need to change it.\n"
            ."Restaurant: Of course. What name is the booking under?\n"
            ."Caller: Patel, table for six at half past seven.\n"
            ."Restaurant: I have it here. What would you like to change?\n"
            ."Caller: We're now eight people, and we'd prefer eight o'clock if you have it.\n"
            ."Restaurant: Eight o'clock for eight is possible, but only in the conservatory, not the main room. Would that be all right?\n"
            ."Caller: That's fine. Is it warm in there?\n"
            ."Restaurant: It's heated, yes. One thing to mention: for parties of eight or more we add a service charge of twelve and a half percent.\n"
            ."Caller: Understood. Also, one of our guests can't eat gluten.\n"
            ."Restaurant: I'll note that. The chef will prepare alternatives, but it helps if you remind us on the day.\n"
            ."Caller: I will. Do you need a deposit?\n"
            ."Restaurant: For eight people we take ten pounds per head, refundable against the bill.",
        'questions' => [
            ['q' => 'What is the new time of the booking?', 'options' => ['7:00', '7:30', '8:00', '8:30'], 'correct' => 2],
            ['q' => 'Where will the group now sit?', 'options' => ['The main room', 'The conservatory', 'The terrace', 'A private room'], 'correct' => 1],
            ['q' => 'When is a service charge added?', 'options' => ['For all bookings', 'For parties of eight or more', 'Only at weekends', 'Only in the main room'], 'correct' => 1],
            ['q' => 'What does the restaurant ask the caller to do on the day?', 'options' => ['Arrive early', 'Pay in cash', 'Remind them about the gluten', 'Confirm by email'], 'correct' => 2],
            ['q' => 'How much is the deposit per person?', 'options' => ['5 pounds', '10 pounds', '12 pounds', '20 pounds'], 'correct' => 1],
        ],
    ],

    // ─── B1: монологи и объявления (Section 2) ───────────────────────────
    [
        'title' => 'Airport Information Announcement',
        'level' => 'B1',
        'text' => "Good afternoon, and welcome to Eastgate Airport. Here is some information to help you through the terminal.\n\n"
            ."Security opens at four in the morning and closes thirty minutes before the final departure. At busy times the queue can take up to forty minutes, so we advise arriving two hours before a European flight and three hours before a long-haul one.\n\n"
            ."Liquids must be in containers of one hundred millilitres or less and carried in a single transparent bag. Laptops and tablets should be removed from hand luggage, but since the new scanners were installed in March, phones and cameras may stay inside.\n\n"
            ."Once through security, you will find the main shopping area on level two, with restaurants on level three. The quiet zone, which has no announcements or music, is at the far end of level three near gate forty.\n\n"
            ."Passengers requiring assistance should use one of the blue help points, and a member of staff will come within ten minutes.\n\n"
            ."Finally, please note that gate numbers are displayed forty minutes before departure and not before. Waiting near a gate before it is announced will not get you on the aircraft any sooner.",
        'questions' => [
            ['q' => 'When does security close?', 'options' => ['At midnight', 'Thirty minutes before the last departure', 'One hour before the last departure', 'It never closes'], 'correct' => 1],
            ['q' => 'How early should passengers arrive for a long-haul flight?', 'options' => ['One hour', 'Two hours', 'Three hours', 'Four hours'], 'correct' => 2],
            ['q' => 'What may now stay inside hand luggage?', 'options' => ['Laptops', 'Tablets', 'Phones and cameras', 'Liquids'], 'correct' => 2],
            ['q' => 'Where is the quiet zone?', 'options' => ['Level two', 'Level three near gate forty', 'Before security', 'Next to the restaurants'], 'correct' => 1],
            ['q' => 'When are gate numbers displayed?', 'options' => ['Two hours before departure', 'One hour before departure', 'Forty minutes before departure', 'At check-in'], 'correct' => 2],
        ],
    ],
    [
        'title' => 'A Guided Walking Tour',
        'level' => 'B1',
        'text' => "Welcome, everyone. My name is Rosa and I'll be leading today's walking tour of the old quarter. The whole route takes about ninety minutes and covers roughly two kilometres, mostly on cobbled streets, so I hope you have sensible shoes.\n\n"
            ."We begin here at the market square, which has held a market every Wednesday since 1348. From here we'll walk up to the cathedral. We won't go inside today, because a service is running, but I'll point out the carvings above the west door, which are the oldest part of the building.\n\n"
            ."After the cathedral we'll follow the old wall down to the river. This is the steepest section, and there are thirty-two steps. If anyone would prefer to avoid them, there's a level path to the left and we'll meet at the bridge.\n\n"
            ."We finish at the harbour, where the tour officially ends. Most people stay for a coffee, and I'm happy to answer questions there.\n\n"
            ."Two practical points. Please keep together at road crossings, and if we become separated, make your way to the bridge rather than back to the square.",
        'questions' => [
            ['q' => 'How long does the tour take?', 'options' => ['Sixty minutes', 'Ninety minutes', 'Two hours', 'Half a day'], 'correct' => 1],
            ['q' => 'Why will the group not enter the cathedral?', 'options' => ['It is closed for repairs', 'A service is taking place', 'It costs extra', 'It opens later'], 'correct' => 1],
            ['q' => 'What is the alternative to the steps?', 'options' => ['A lift', 'A level path to the left', 'A bus', 'Waiting at the cathedral'], 'correct' => 1],
            ['q' => 'Where does the tour end?', 'options' => ['The market square', 'The cathedral', 'The bridge', 'The harbour'], 'correct' => 3],
            ['q' => 'What should anyone who gets separated do?', 'options' => ['Return to the square', 'Go to the bridge', 'Phone the guide', 'Wait where they are'], 'correct' => 1],
        ],
    ],
    [
        'title' => 'Instructions for a Cookery Class',
        'level' => 'B1',
        'text' => "Right, everyone, gather round before we start. A few things you need to know about the session.\n\n"
            ."Today we're making three dishes: a soup, a bread and a simple dessert. You'll work in pairs, and each pair has a station with everything already weighed out. Please don't move ingredients between stations, because the quantities are different for the left and right sides of the room.\n\n"
            ."The knives are extremely sharp. Cut on the board, never in your hand, and if a knife falls, step back and let it fall. Do not try to catch it.\n\n"
            ."The ovens are already at two hundred degrees. Use the thick blue gloves, not the tea towels — a damp towel conducts heat and will burn you.\n\n"
            ."We break at half past three for twenty minutes. The bread needs to prove during that time, so mix it before the break, not after.\n\n"
            ."At the end you take your food home in the boxes provided. Please wash your own station; the class after ours starts twenty minutes later.",
        'questions' => [
            ['q' => 'How are participants organised?', 'options' => ['Individually', 'In pairs', 'In groups of four', 'By experience level'], 'correct' => 1],
            ['q' => 'Why must ingredients stay at their own station?', 'options' => ['For hygiene', 'The quantities differ between sides of the room', 'They are numbered', 'To save time'], 'correct' => 1],
            ['q' => 'What should you do if a knife falls?', 'options' => ['Catch it', 'Step back', 'Kick it away', 'Call the teacher'], 'correct' => 1],
            ['q' => 'Why should tea towels not be used for the oven?', 'options' => ['They are dirty', 'They are too small', 'A damp towel conducts heat', 'They belong to the kitchen'], 'correct' => 2],
            ['q' => 'When should the bread be mixed?', 'options' => ['Before the break', 'After the break', 'At the very start', 'At the end'], 'correct' => 0],
        ],
    ],
    [
        'title' => 'Registering at a Health Centre',
        'level' => 'B1',
        'text' => "Receptionist: Good morning. How can I help?\n"
            ."Patient: I've just moved to the area and I'd like to register with the practice.\n"
            ."Receptionist: Certainly. Do you live within our catchment area? That's anywhere with a postcode beginning HD4 or HD5.\n"
            ."Patient: Mine is HD4, yes.\n"
            ."Receptionist: Good. You'll need to fill in this form and bring proof of address — a tenancy agreement or a utility bill will do. A bank statement is also accepted, but it must be less than three months old.\n"
            ."Patient: I have a tenancy agreement with me.\n"
            ."Receptionist: Perfect. Once you're registered we'll invite you for a new patient check with the practice nurse. It takes about twenty minutes and covers blood pressure, height and weight, and a short questionnaire.\n"
            ."Patient: How do I book an appointment with a doctor?\n"
            ."Receptionist: Routine appointments are booked online or by phone up to four weeks ahead. For anything urgent, ring at eight in the morning and you'll be offered a same-day slot.\n"
            ."Patient: And repeat prescriptions?\n"
            ."Receptionist: Through the app, and please allow two working days.",
        'questions' => [
            ['q' => 'Which postcodes does the practice cover?', 'options' => ['HD1 and HD2', 'HD4 and HD5', 'Any postcode', 'HD5 only'], 'correct' => 1],
            ['q' => 'What is said about a bank statement as proof?', 'options' => ['It is not accepted', 'It must be under three months old', 'It must be stamped', 'Only originals are accepted'], 'correct' => 1],
            ['q' => 'What does the new patient check include?', 'options' => ['A blood test', 'Blood pressure, height and weight', 'An X-ray', 'A dental check'], 'correct' => 1],
            ['q' => 'When should patients ring for an urgent appointment?', 'options' => ['Any time', 'At eight in the morning', 'After lunch', 'The day before'], 'correct' => 1],
            ['q' => 'How long do repeat prescriptions take?', 'options' => ['Same day', 'One working day', 'Two working days', 'One week'], 'correct' => 2],
        ],
    ],
    [
        'title' => 'Community Centre Timetable',
        'level' => 'B1',
        'text' => "Thank you all for coming to this short introduction to the community centre. Let me run through what happens here each week.\n\n"
            ."Monday evenings are given over to the choir, which is open to anyone and needs no audition. Tuesday mornings we run a parent and toddler group; there's a small charge of two pounds per family, which covers refreshments.\n\n"
            ."Wednesday is the busiest day. The computer help sessions run from ten until noon, and volunteers will sit with you individually rather than teach a class. In the afternoon the hall becomes an indoor market for local producers.\n\n"
            ."Thursday evening is currently free. We are looking for suggestions from residents, and there's a box by the door for ideas.\n\n"
            ."Friday afternoons we host a lunch club for anyone over sixty-five. It costs four pounds including a two-course meal, and transport can be arranged for those who cannot easily get here.\n\n"
            ."At weekends the hall is available for private hire at thirty pounds an hour, reduced to twenty for residents of the parish.",
        'questions' => [
            ['q' => 'What is required to join the choir?', 'options' => ['An audition', 'Nothing — it is open to all', 'A membership fee', 'Previous experience'], 'correct' => 1],
            ['q' => 'How are the computer sessions run?', 'options' => ['As a lecture', 'Individually with volunteers', 'Online only', 'In small classes'], 'correct' => 1],
            ['q' => 'What is happening on Thursday evenings?', 'options' => ['A choir practice', 'A market', 'Nothing yet — suggestions wanted', 'A lunch club'], 'correct' => 2],
            ['q' => 'What does the lunch club cost?', 'options' => ['Two pounds', 'Four pounds', 'Six pounds', 'It is free'], 'correct' => 1],
            ['q' => 'How much is private hire for parish residents?', 'options' => ['Twenty pounds an hour', 'Thirty pounds an hour', 'Forty pounds an hour', 'It is free'], 'correct' => 0],
        ],
    ],

    // ─── B2: учебные разговоры (Section 3) ───────────────────────────────
    [
        'title' => 'Planning Field Research',
        'level' => 'B2',
        'text' => "Supervisor: So, you two are planning fieldwork on urban bird populations. Where are you up to?\n"
            ."Daniel: We've chosen four sites — two parks, a cemetery and an industrial estate.\n"
            ."Supervisor: Why the industrial estate?\n"
            ."Maya: As a contrast. Everyone surveys green space, so nobody knows what actually lives on brownfield land.\n"
            ."Supervisor: That's a defensible reason. How often will you visit?\n"
            ."Daniel: We thought once a week for eight weeks.\n"
            ."Supervisor: Once a week is thin. Bird activity varies enormously with weather, and eight visits gives you no way to separate a weather effect from a site effect. Twice a week for six weeks would be stronger, even though it is fewer total weeks.\n"
            ."Maya: We could manage that if we split the sites between us.\n"
            ."Supervisor: Don't. If you each survey different sites, any difference you find might just be the difference between the two of you as observers. Do the first three visits together to calibrate, then swap sites halfway through.\n"
            ."Daniel: That doubles the travel.\n"
            ."Supervisor: It does. But a study you cannot defend is not worth the travel you save.",
        'questions' => [
            ['q' => 'Why did the students include an industrial estate?', 'options' => ['It is close to the university', 'Brownfield land is under-surveyed', 'It has the most birds', 'The supervisor suggested it'], 'correct' => 1],
            ['q' => 'What is the supervisor concerned about with weekly visits?', 'options' => ['The cost', 'Separating weather effects from site effects', 'Student safety', 'Equipment availability'], 'correct' => 1],
            ['q' => 'What does the supervisor recommend?', 'options' => ['Twice a week for six weeks', 'Once a week for twelve weeks', 'Daily visits', 'Fewer sites'], 'correct' => 0],
            ['q' => 'Why should the students not split the sites permanently?', 'options' => ['It takes longer', 'Observer differences would confound the results', 'The equipment cannot be shared', 'The supervisor cannot check both'], 'correct' => 1],
            ['q' => 'What is the supervisor final point?', 'options' => ['Travel time matters most', 'An indefensible study wastes the effort saved', 'Fewer sites are always better', 'Weather should be ignored'], 'correct' => 1],
        ],
    ],
    [
        'title' => 'Discussing a Project Deadline',
        'level' => 'B2',
        'text' => "Tutor: You asked to see me about the deadline.\n"
            ."Priya: Yes. We're two weeks from submission and the data collection isn't finished.\n"
            ."Tutor: What's outstanding?\n"
            ."Omar: We have eighty of the hundred and twenty responses we planned. The last forty are coming from one partner organisation that has gone quiet.\n"
            ."Tutor: Have you chased them?\n"
            ."Priya: Twice by email.\n"
            ."Tutor: Then phone. Email is easy to ignore. But let's assume they never reply. Is eighty responses enough to say anything?\n"
            ."Omar: It's enough for the descriptive part. The comparison between the two groups would be underpowered.\n"
            ."Tutor: Then write it as a smaller study rather than a broken large one. Report eighty, state the intended sample and why it was not reached, and drop the comparison to a limitation rather than a finding.\n"
            ."Priya: Won't that lose marks?\n"
            ."Tutor: Far fewer than presenting an underpowered comparison as though it were solid. Examiners reward candour about limits; they punish claims the data cannot carry.\n"
            ."Omar: And an extension?\n"
            ."Tutor: Possible for two weeks with evidence of the delay, but the same problem waits at the end of it if the organisation still does not answer.",
        'questions' => [
            ['q' => 'How many responses have the students collected?', 'options' => ['40', '80', '100', '120'], 'correct' => 1],
            ['q' => 'What does the tutor suggest instead of emailing?', 'options' => ['Visiting in person', 'Phoning', 'Writing a letter', 'Giving up'], 'correct' => 1],
            ['q' => 'What should happen to the group comparison?', 'options' => ['Present it as a finding', 'Move it to the limitations', 'Delete all mention of it', 'Collect more data first'], 'correct' => 1],
            ['q' => 'What does the tutor say about examiners?', 'options' => ['They ignore limitations', 'They reward candour about limits', 'They only count sample size', 'They prefer larger claims'], 'correct' => 1],
            ['q' => 'What is the risk with an extension?', 'options' => ['It costs money', 'The same problem may remain', 'It is never granted', 'It reduces the word limit'], 'correct' => 1],
        ],
    ],
    [
        'title' => 'Choosing Modules with an Adviser',
        'level' => 'B2',
        'text' => "Adviser: You need to choose two optional modules for next semester. Any thoughts?\n"
            ."Lena: I'm drawn to Advanced Statistics and Media Ethics, but they look very different.\n"
            ."Adviser: They are, and that isn't necessarily a problem. What do you want to do afterwards?\n"
            ."Lena: Probably something in journalism, possibly data journalism.\n"
            ."Adviser: Then both are relevant, and the combination is more useful than either alone. But check the assessment. Advanced Statistics is a three-hour examination; Media Ethics is two essays and a presentation.\n"
            ."Lena: That's a lot of writing in one semester.\n"
            ."Adviser: It is. Look at the deadlines: the Media Ethics essays fall in weeks six and eleven, and the statistics exam is in the assessment period. They don't collide, which is the main thing.\n"
            ."Lena: What about the prerequisite for statistics?\n"
            ."Adviser: You need a pass in the first-year quantitative module. You got sixty-eight, so that's fine.\n"
            ."Lena: And if I find it too difficult?\n"
            ."Adviser: You can switch modules until the end of week two, no penalty. After that, a withdrawal is recorded on your transcript.",
        'questions' => [
            ['q' => 'What career is the student considering?', 'options' => ['Teaching', 'Data journalism', 'Accountancy', 'Law'], 'correct' => 1],
            ['q' => 'How is Media Ethics assessed?', 'options' => ['One exam', 'Two essays and a presentation', 'Coursework only', 'A dissertation'], 'correct' => 1],
            ['q' => 'What does the adviser say about the deadlines?', 'options' => ['They clash badly', 'They do not collide', 'They are unknown', 'They can be moved'], 'correct' => 1],
            ['q' => 'What is the prerequisite for Advanced Statistics?', 'options' => ['A pass in the quantitative module', 'A distinction in any module', 'Two years of study', 'None'], 'correct' => 0],
            ['q' => 'Until when can modules be changed without penalty?', 'options' => ['End of week one', 'End of week two', 'End of week six', 'Any time'], 'correct' => 1],
        ],
    ],
    [
        'title' => 'Feedback on a Draft Essay',
        'level' => 'B2',
        'text' => "Tutor: I've read the draft. The research is genuinely good, and the structure is letting it down.\n"
            ."Sam: In what way?\n"
            ."Tutor: Your argument doesn't appear until page four. Everything before that is background, and a reader cannot tell what the background is for.\n"
            ."Sam: I thought context should come first.\n"
            ."Tutor: Context should come first, but it should be selected by the argument it supports. At the moment you have summarised everything you read, whether or not it bears on your claim. Cut roughly half of section one and state your thesis on the first page.\n"
            ."Sam: What about the middle section? I spent longest on that.\n"
            ."Tutor: It's the strongest part, and it needs almost nothing. One caution: you write that the data prove your point. They support it. Prove is a word to use very sparingly.\n"
            ."Sam: And the conclusion?\n"
            ."Tutor: It repeats the introduction. A conclusion should say what follows from what you have shown, not summarise it again. What should the reader do differently having read this?\n"
            ."Sam: I hadn't thought of it that way.\n"
            ."Tutor: Answer that question in two sentences and you have your conclusion.",
        'questions' => [
            ['q' => 'What is the main problem with the draft?', 'options' => ['Weak research', 'The structure', 'Poor spelling', 'Too short'], 'correct' => 1],
            ['q' => 'Where should the thesis appear?', 'options' => ['On page four', 'On the first page', 'In the conclusion', 'In a footnote'], 'correct' => 1],
            ['q' => 'What does the tutor say about the word prove?', 'options' => ['Use it more', 'Use it very sparingly', 'It is always wrong', 'It is fine as used'], 'correct' => 1],
            ['q' => 'What is wrong with the conclusion?', 'options' => ['It is too long', 'It repeats the introduction', 'It contradicts the essay', 'It is missing'], 'correct' => 1],
            ['q' => 'What should a conclusion do?', 'options' => ['Summarise the essay', 'Say what follows from the argument', 'Introduce new research', 'List the sources'], 'correct' => 1],
        ],
    ],
    [
        'title' => 'Arranging a Work Placement',
        'level' => 'B2',
        'text' => "Coordinator: You've been offered the placement at the environmental consultancy. Congratulations.\n"
            ."Tom: Thank you. What happens now?\n"
            ."Coordinator: Three things. The learning agreement, the risk assessment and the insurance check. The agreement is the important one — it sets out what you will actually do.\n"
            ."Tom: They mentioned data analysis and some site visits.\n"
            ."Coordinator: Get that in writing. Placements drift, and students who arrive with a vague agreement often spend twelve weeks doing administration. If the agreement says data analysis, you have something to point at.\n"
            ."Tom: Do I write it or do they?\n"
            ."Coordinator: You draft it, they amend it, we all sign. It's easier to argue about a draft than about nothing.\n"
            ."Tom: And the site visits — is there anything I need?\n"
            ."Coordinator: Safety boots and a hard hat, which the company usually provides. Check before you buy anything. The risk assessment covers the site work, and they should already have one; ask for a copy rather than writing your own.\n"
            ."Tom: How much of my final mark does the placement carry?\n"
            ."Coordinator: Thirty percent, assessed on your reflective report rather than on the employer's opinion of you.",
        'questions' => [
            ['q' => 'Which document does the coordinator call the important one?', 'options' => ['The risk assessment', 'The learning agreement', 'The insurance check', 'The reflective report'], 'correct' => 1],
            ['q' => 'What can happen without a clear agreement?', 'options' => ['The placement is cancelled', 'The student ends up doing administration', 'The pay is reduced', 'The placement is shortened'], 'correct' => 1],
            ['q' => 'Who drafts the agreement first?', 'options' => ['The company', 'The student', 'The university', 'A lawyer'], 'correct' => 1],
            ['q' => 'What should the student do about the risk assessment?', 'options' => ['Write his own', 'Ask the company for a copy', 'Ignore it', 'Pay for one'], 'correct' => 1],
            ['q' => 'How is the placement assessed?', 'options' => ['By the employer opinion', 'By the reflective report', 'By an exam', 'By attendance'], 'correct' => 1],
        ],
    ],

    // ─── C1: академические лекции (Section 4) ────────────────────────────
    [
        'title' => 'Lecture: The Invention of Standard Time',
        'level' => 'C1',
        'text' => "Until the middle of the nineteenth century, time was local. Noon was the moment the sun stood highest over your particular town, which meant that clocks in one city differed from those a hundred miles away by several minutes. For most of history this caused nobody any difficulty, because nobody could travel fast enough for the difference to matter.\n\n"
            ."The railway changed that. A train leaving one city at ten and arriving at another at noon was using two different noons, and timetables became ambiguous in ways that were not merely inconvenient but dangerous. On single-track lines, two trains proceeding under slightly different clocks could occupy the same stretch of rail.\n\n"
            ."British railway companies adopted London time in the 1840s, decades before Parliament made it official. Note the sequence: the standard was created by the industry that needed it, and the law followed.\n\n"
            ."The international system came later, at a conference in Washington in 1884, which fixed the meridian at Greenwich. That choice was not scientific. Greenwich was selected largely because most of the world's shipping already used charts based on it, and the cost of changing them was prohibitive.\n\n"
            ."The lesson worth taking is that standards are rarely adopted because they are optimal. They are adopted because switching away from them has become too expensive — a pattern you will meet again in the history of almost every technology.",
        'questions' => [
            ['q' => 'What determined local time before the nineteenth century?', 'options' => ['The nearest capital city', 'The position of the sun over that town', 'Church bells', 'The railway timetable'], 'correct' => 1],
            ['q' => 'Why was time confusion dangerous on railways?', 'options' => ['Passengers missed trains', 'Two trains could occupy the same track', 'Fares were miscalculated', 'Signals failed'], 'correct' => 1],
            ['q' => 'What sequence does the lecturer highlight?', 'options' => ['The law came first, then industry', 'Industry created the standard and the law followed', 'Both happened at once', 'Neither happened deliberately'], 'correct' => 1],
            ['q' => 'Why was Greenwich chosen?', 'options' => ['It was scientifically superior', 'Most shipping charts already used it', 'It is on the equator', 'It was a political compromise between France and Germany'], 'correct' => 1],
            ['q' => 'What general lesson does the lecturer draw?', 'options' => ['Standards win by being optimal', 'Standards persist because switching becomes too costly', 'Standards are always set by governments', 'Standards rarely last'], 'correct' => 1],
        ],
    ],
    [
        'title' => 'Lecture: Why Bridges Fail',
        'level' => 'C1',
        'text' => "When a bridge collapses, the public assumes the cause was an error of calculation. In the recorded history of major failures, that is rarely what happened.\n\n"
            ."Consider the Tacoma Narrows bridge, which tore itself apart in 1940. The arithmetic was sound. What the designers had not accounted for was aerodynamic behaviour — a mode of oscillation that the mathematics of the time did not model, because no bridge had ever been slender enough for it to matter. The failure was not a mistake within the theory; it was the theory reaching the edge of its applicability without anyone noticing.\n\n"
            ."A second pattern is more mundane and more common: maintenance. Steel corrodes, concrete admits water, bearings seize. These processes are slow, visible and cheap to arrest early, and they are precisely the ones deferred when budgets tighten. The Genoa collapse in 2018 followed years of documented concern.\n\n"
            ."A third pattern is organisational. In several inquiries the technical warning existed, in writing, from an engineer who understood the risk — and did not reach anyone with the authority to stop the work. The information was present in the organisation and absent from the decision.\n\n"
            ."If you take one thing from today, let it be this: the interesting question after a failure is rarely who calculated wrongly. It is why the correct calculation did not change anything.",
        'questions' => [
            ['q' => 'What does the public usually assume causes collapses?', 'options' => ['Bad weather', 'A calculation error', 'Poor materials', 'Overloading'], 'correct' => 1],
            ['q' => 'What caused the Tacoma Narrows failure?', 'options' => ['Arithmetic mistakes', 'Aerodynamic behaviour outside existing theory', 'Corrosion', 'Excessive traffic'], 'correct' => 1],
            ['q' => 'Why is maintenance deferred, according to the lecturer?', 'options' => ['It is technically difficult', 'It is expensive early on', 'Budgets tighten and the processes are slow', 'Engineers disagree about it'], 'correct' => 2],
            ['q' => 'What was the organisational pattern in several inquiries?', 'options' => ['No one understood the risk', 'The warning existed but did not reach decision-makers', 'The warnings were fabricated', 'Engineers refused to report'], 'correct' => 1],
            ['q' => 'What does the lecturer call the interesting question?', 'options' => ['Who calculated wrongly', 'Why the correct calculation changed nothing', 'How much the repair costs', 'When the bridge was built'], 'correct' => 1],
        ],
    ],
    [
        'title' => 'Lecture: The Economics of Antibiotics',
        'level' => 'C1',
        'text' => "Antibiotic resistance is usually presented as a biological problem. It is also, and perhaps primarily, an economic one, and understanding why explains the shape of the crisis.\n\n"
            ."Begin with the incentive facing a pharmaceutical company. A new antibiotic, if approved, will be deliberately withheld. Doctors will reserve it for cases where nothing else works, precisely because using it widely would breed resistance to it. So the company has invested a decade and a great deal of money to produce a product whose responsible use guarantees low sales.\n\n"
            ."Compare that with a drug for a chronic condition, taken daily for thirty years. The commercial logic is unambiguous, and the pipeline reflects it: the number of large firms with active antibiotic programmes has fallen by roughly ninety percent since the 1980s.\n\n"
            ."Several remedies have been proposed. Subscription models pay a fixed annual sum for access regardless of volume, breaking the link between revenue and quantity prescribed. Market entry rewards pay a lump sum on approval. Both attempt the same thing: to make developing a drug worthwhile without making selling more of it worthwhile.\n\n"
            ."Note what this implies. The usual assumption that market demand will call forth supply fails here in a specific and instructive way, because the socially optimal quantity of the product is close to zero until the moment it is desperately needed.",
        'questions' => [
            ['q' => 'Why will a new antibiotic be withheld?', 'options' => ['It is unsafe', 'Wide use would breed resistance', 'It is too expensive', 'Regulators demand it'], 'correct' => 1],
            ['q' => 'How has the number of firms with antibiotic programmes changed since the 1980s?', 'options' => ['Risen by half', 'Stayed the same', 'Fallen by about ninety percent', 'Doubled'], 'correct' => 2],
            ['q' => 'What does a subscription model do?', 'options' => ['Raises the price per dose', 'Breaks the link between revenue and quantity prescribed', 'Bans the drug', 'Funds only research'], 'correct' => 1],
            ['q' => 'What do both proposed remedies attempt?', 'options' => ['To increase prescriptions', 'To reward development without rewarding volume', 'To reduce research costs', 'To speed up approval'], 'correct' => 1],
            ['q' => 'Why does ordinary market logic fail here?', 'options' => ['Patents are too short', 'The optimal quantity is near zero until needed', 'Manufacturing is impossible', 'Demand is unmeasurable'], 'correct' => 1],
        ],
    ],
    [
        'title' => 'Lecture: How Memory Reconstructs the Past',
        'level' => 'C1',
        'text' => "The intuitive model of memory is a recording. You experience an event, the recording is stored, and remembering plays it back, perhaps with some loss of quality. Almost every part of that model is wrong.\n\n"
            ."Memory is reconstructive. Each time you recall an episode, you rebuild it from fragments, and the rebuilt version is what gets stored again. This means that recall is not a neutral act of reading; it is an act of writing. A memory frequently revisited is not better preserved for having been revisited. It has been repeatedly rewritten.\n\n"
            ."The practical consequences are considerable. Eyewitness confidence rises with repetition even when accuracy does not, which is why a witness who has told the story twenty times may be both more certain and less reliable than one telling it for the first time. Courts have historically treated confidence as a proxy for accuracy; the evidence does not support that.\n\n"
            ."Nor is misremembering a malfunction. A system that stored everything faithfully would be enormous and largely useless. Reconstruction allows generalisation — extracting what tends to happen from what happened once — and generalisation is what makes memory useful for prediction rather than merely for nostalgia.\n\n"
            ."So the errors are not defects in the system. They are the price of the feature.",
        'questions' => [
            ['q' => 'What is the intuitive model of memory?', 'options' => ['A reconstruction', 'A recording played back', 'A generalisation', 'A prediction'], 'correct' => 1],
            ['q' => 'What happens each time an episode is recalled?', 'options' => ['It is preserved unchanged', 'It is rebuilt and stored again', 'It is deleted', 'It becomes more accurate'], 'correct' => 1],
            ['q' => 'What does repetition do to eyewitness testimony?', 'options' => ['Raises accuracy and confidence together', 'Raises confidence without raising accuracy', 'Lowers confidence', 'Has no effect'], 'correct' => 1],
            ['q' => 'Why would perfect storage be useless?', 'options' => ['It would be too slow', 'It would be enormous and prevent generalisation', 'It would be inaccurate', 'It would require more sleep'], 'correct' => 1],
            ['q' => 'How does the lecturer characterise memory errors?', 'options' => ['As defects to be corrected', 'As the price of a useful feature', 'As rare anomalies', 'As evidence of illness'], 'correct' => 1],
        ],
    ],
    [
        'title' => 'Lecture: How Writing Systems Spread',
        'level' => 'C1',
        'text' => "Writing has been invented independently perhaps three times in human history — in Mesopotamia, in China, and in Mesoamerica. Everything else, including the alphabet you are reading now, is descended by borrowing. That ratio of three inventions to thousands of derived scripts is the central fact of the field.\n\n"
            ."The reason is that inventing writing is extraordinarily difficult, whereas adapting an existing system is merely laborious. Once a community has seen that marks can encode speech, the hardest conceptual step has already been taken for them. Cultures that encountered writing generally borrowed the idea and then modified the signs, rather than starting again.\n\n"
            ."The adaptations are revealing. When a script designed for one language is applied to another, the fit is never exact, and the mismatches persist for centuries. English spelling preserves the sound system of a language spoken six hundred years ago because the writing was fixed and the speech moved on.\n\n"
            ."There is a further pattern. Scripts spread along the routes of trade and religion rather than conquest. Empires imposed administration; they rarely imposed a writing system successfully. What travelled was the script that people had a daily reason to read.\n\n"
            ."The general point is that a technology diffuses along the lines of its usefulness to ordinary people, and only incidentally along the lines drawn on political maps.",
        'questions' => [
            ['q' => 'How many times was writing invented independently?', 'options' => ['Once', 'Perhaps three times', 'Ten times', 'It is unknown'], 'correct' => 1],
            ['q' => 'Why did cultures borrow rather than invent?', 'options' => ['Borrowing was forbidden', 'The hardest conceptual step was already taken', 'They lacked materials', 'Invention was illegal'], 'correct' => 1],
            ['q' => 'Why does English spelling look irregular?', 'options' => ['It was designed badly', 'The writing was fixed while speech changed', 'It borrowed from Chinese', 'It was never standardised'], 'correct' => 1],
            ['q' => 'Along what routes did scripts mainly spread?', 'options' => ['Military conquest', 'Trade and religion', 'Royal decree', 'Migration only'], 'correct' => 1],
            ['q' => 'What is the general point of the lecture?', 'options' => ['Technology spreads by political power', 'Technology spreads along lines of everyday usefulness', 'Writing systems rarely change', 'Empires determine language'], 'correct' => 1],
        ],
    ],
];
