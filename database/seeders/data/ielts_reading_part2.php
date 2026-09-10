<?php

/** Вторая половина текстов IELTS Reading — уровни B2 и C1. */

return [
    [
        'title' => 'The Limits of Recycling Plastic',
        'level' => 'B2',
        'text' => "Recycling is presented to consumers as the responsible endpoint of plastic use, and for some materials that description is accurate. Aluminium can be melted and recast indefinitely with negligible loss of quality. Glass behaves similarly. Plastic does not.\n\n"
            ."The difficulty is chemical. Most plastics are polymers whose chains shorten each time the material is melted, and shorter chains mean weaker material. A bottle recycled once may become a bottle; recycled again it becomes fibre, then perhaps a park bench, then nothing. The process is more accurately called downcycling, and it postpones disposal rather than preventing it.\n\n"
            ."Sorting compounds the problem. There are several commercially significant plastic types and they cannot be melted together, because a small contamination of one in another ruins the batch. Automated sorting has improved considerably, but a food container made of two bonded plastics is effectively unsortable at any realistic cost.\n\n"
            ."Economics then decides the outcome. Recycled plastic competes against virgin plastic made from oil, and when the oil price is low the recycled material is more expensive to produce than the new. Collection continues because it is publicly mandated; processing does not always follow, which is how collected material has periodically ended up in landfill or exported.\n\n"
            ."None of this makes collection pointless. It does suggest that the order of the familiar three words is doing real work. Reduce and reuse act on the quantity of material in circulation; recycling acts only on its destination, and imperfectly at that.",
        'questions' => [
            ['q' => 'Why can aluminium be recycled indefinitely?', 'options' => ['It is cheaper', 'It loses negligible quality when recast', 'It is sorted easily', 'It is rarely contaminated'], 'correct' => 1],
            ['q' => 'What happens to polymer chains during melting?', 'options' => ['They lengthen', 'They shorten, weakening the material', 'They stay identical', 'They change colour'], 'correct' => 1],
            ['q' => 'Why are bonded multi-plastic containers a problem?', 'options' => ['They are heavy', 'They are effectively unsortable at realistic cost', 'They are banned', 'They melt too fast'], 'correct' => 1],
            ['q' => 'When is recycled plastic uncompetitive?', 'options' => ['When oil prices are low', 'When oil prices are high', 'In winter', 'When demand is high'], 'correct' => 0],
            ['q' => 'What does the author say about the familiar three words?', 'options' => ['Their order is arbitrary', 'Their order reflects real differences in effect', 'Recycling should come first', 'They are outdated'], 'correct' => 1],
        ],
    ],
    [
        'title' => 'How Fermentation Shaped Food',
        'level' => 'B2',
        'text' => "Almost every food culture in the world independently developed fermentation, and the convergence is not coincidental. Before refrigeration, a controlled microbial process was one of very few reliable ways of keeping food edible beyond a season.\n\n"
            ."The mechanism is competitive exclusion. Introducing a population of organisms that produce acid or alcohol creates an environment in which spoilage organisms and pathogens cannot establish themselves. Cabbage submerged in brine ferments into something that keeps for months; the same cabbage left in air rots within days. The difference is not the absence of microbes but the presence of the right ones.\n\n"
            ."Preservation was the original purpose, but it was not the only consequence. Fermentation transforms flavour by breaking proteins into amino acids, several of which are strongly savoury. It also makes some foods more digestible: fermented soy is more available nutritionally than the raw bean, and traditional bread leavening reduces compounds that inhibit mineral absorption.\n\n"
            ."What is striking historically is how much was achieved without any understanding of the mechanism. The microbial explanation dates from the nineteenth century; the techniques are thousands of years older. Practitioners had reliable procedures — temperatures, salt concentrations, vessels, timings — arrived at by iteration and transmitted as craft.\n\n"
            ."Modern industrial production reversed the relationship. Cultures are now selected and inoculated deliberately, which gives consistency and removes risk, at the cost of the regional variation that arose when each locality fermented with whatever organisms happened to live there.",
        'questions' => [
            ['q' => 'Why did fermentation develop across cultures?', 'options' => ['Trade spread it', 'It was one of few reliable preservation methods', 'It was cheap', 'Religion required it'], 'correct' => 1],
            ['q' => 'What is competitive exclusion?', 'options' => ['Removing all microbes', 'Introducing organisms that prevent spoilage organisms establishing', 'Heating food thoroughly', 'Drying food completely'], 'correct' => 1],
            ['q' => 'What does fermentation do to proteins?', 'options' => ['Destroys them', 'Breaks them into savoury amino acids', 'Makes them indigestible', 'Leaves them unchanged'], 'correct' => 1],
            ['q' => 'What is historically striking?', 'options' => ['Techniques predate the explanation by millennia', 'The explanation came first', 'The methods were identical everywhere', 'It was discovered recently'], 'correct' => 0],
            ['q' => 'What has industrial production cost?', 'options' => ['Consistency', 'Safety', 'Regional variation', 'Nutritional value'], 'correct' => 2],
        ],
    ],
    [
        'title' => 'The Placebo Effect and Its Discontents',
        'level' => 'C1',
        'text' => "The placebo effect is among the most cited and least precisely understood findings in medicine. In its popular form it holds that belief in a treatment produces improvement. The literature supports a considerably narrower claim.\n\n"
            ."Much of what is casually attributed to placebo is not an effect of the placebo at all. Many conditions improve without intervention, and patients typically enter trials when symptoms are at their worst, so measurement at any later point will show improvement regardless of treatment. Disentangling this regression from a genuine response requires a no-treatment arm, which most trials omit for reasons of cost and ethics.\n\n"
            ."Where careful comparisons have been made, the response is real but selective. It is reliably observed for outcomes involving subjective report — pain, nausea, fatigue — and largely absent for outcomes measured independently of the patient, such as tumour size or blood chemistry. This asymmetry is informative rather than embarrassing: it suggests a mechanism operating on the perception and reporting of symptoms rather than on the underlying pathology.\n\n"
            ."Recent work on open-label placebos complicates the picture usefully. Patients told explicitly that they are receiving an inert substance nonetheless report improvement in some conditions, which is difficult to reconcile with an explanation based purely on deception or expectation.\n\n"
            ."The methodological consequence matters more than the philosophical one. Because a placebo arm captures both genuine placebo response and natural improvement, a drug that merely equals placebo has not been shown to do nothing. It has been shown to add nothing — a distinction routinely lost in reporting.",
        'questions' => [
            ['q' => 'What is often misattributed to the placebo effect?', 'options' => ['Side effects', 'Natural improvement and regression to the mean', 'Measurement error only', 'Drug interactions'], 'correct' => 1],
            ['q' => 'What is needed to separate genuine placebo response?', 'options' => ['A larger sample', 'A no-treatment arm', 'Longer follow-up', 'Blinded assessors'], 'correct' => 1],
            ['q' => 'For which outcomes is the response reliably observed?', 'options' => ['Tumour size', 'Blood chemistry', 'Subjective reports such as pain', 'Survival rates'], 'correct' => 2],
            ['q' => 'Why do open-label placebos complicate the picture?', 'options' => ['They are unethical', 'Improvement occurs despite patients knowing', 'They are never effective', 'They cost more'], 'correct' => 1],
            ['q' => 'What distinction does the author say is routinely lost?', 'options' => ['Between placebo and no treatment', 'Between doing nothing and adding nothing', 'Between pain and nausea', 'Between trials and observation'], 'correct' => 1],
        ],
    ],
    [
        'title' => 'Why Predictions Fail',
        'level' => 'C1',
        'text' => "The systematic study of forecasting accuracy produces a consistent and uncomfortable result. Expert predictions in politics and economics perform, on average, only marginally better than simple statistical rules, and in some domains worse.\n\n"
            ."The explanation is not that experts know less than they claim. It is that expertise in a domain and skill at forecasting within it are different capacities, and the first can actively impede the second. Detailed knowledge supplies a rich supply of reasons why any particular outcome should follow, and a forecaster who can construct a compelling narrative for a scenario tends to overweight it.\n\n"
            ."Work on forecasting tournaments has identified the characteristics of the minority who do consistently better. They are not more knowledgeable. They update more readily on small pieces of evidence, they express probabilities in finer gradations rather than defaulting to round numbers, and they treat a question as a set of components to be estimated separately rather than as a single judgement.\n\n"
            ."They also share a habit that is easy to state and difficult to practise: they begin with the base rate. Asked how likely a start-up is to survive five years, they start with the proportion of start-ups that do, and adjust from there. The unsuccessful forecaster starts with the specific company and rarely returns to the general case.\n\n"
            ."The implication for institutions is that forecasting skill is measurable and trainable, yet almost no organisation records the accuracy of its own predictions. Where no score is kept, confidence rather than calibration determines whose forecasts are believed.",
        'questions' => [
            ['q' => 'How do expert predictions perform on average?', 'options' => ['Far better than statistical rules', 'Only marginally better, sometimes worse', 'Perfectly', 'They cannot be measured'], 'correct' => 1],
            ['q' => 'How can expertise impede forecasting?', 'options' => ['It reduces available data', 'It supplies reasons that make any scenario compelling', 'It slows analysis', 'It prevents updating entirely'], 'correct' => 1],
            ['q' => 'What distinguishes better forecasters?', 'options' => ['Greater knowledge', 'Readier updating and finer probability gradations', 'Stronger convictions', 'Longer experience'], 'correct' => 1],
            ['q' => 'What habit do good forecasters share?', 'options' => ['Starting from the specific case', 'Starting from the base rate', 'Avoiding numbers', 'Consulting more experts'], 'correct' => 1],
            ['q' => 'What happens where no accuracy score is kept?', 'options' => ['Calibration improves', 'Confidence determines who is believed', 'Forecasts are ignored', 'Experts are replaced'], 'correct' => 1],
        ],
    ],
    [
        'title' => 'The Invention of Childhood',
        'level' => 'C1',
        'text' => "The claim that childhood is a historical invention rather than a biological given has been influential and frequently misunderstood. Nobody disputes that children have always been physically immature. What is at issue is whether childhood has always been treated as a distinct stage with its own institutions, obligations and protections.\n\n"
            ."The evidence for change is substantial. In much of pre-industrial Europe, children joined adult labour as soon as they were physically able, wore adult clothing in miniature, and appear in legal records under the same categories as adults. Specialised institutions — age-graded schooling, separate legal treatment, dedicated literature and clothing — emerged largely between the seventeenth and nineteenth centuries.\n\n"
            ."The original formulation of the thesis went further, arguing that pre-modern societies lacked a concept of childhood and, by implication, strong parental affection. That stronger claim has not survived scrutiny. Diaries, letters and the grave goods of children indicate grief and attachment throughout the period, and historians now generally treat the argument as concerning institutions rather than emotions.\n\n"
            ."The revised version remains consequential. If childhood is institutional, its boundaries are negotiable, and the historical record shows them moving repeatedly: the age of criminal responsibility, of employment, of consent and of majority have all shifted, and they do not move together or in the same direction.\n\n"
            ."That variability is worth holding in mind whenever a current boundary is defended as natural. Most such boundaries are recent, were contested when introduced, and differ between neighbouring countries with no evident difference in the children concerned.",
        'questions' => [
            ['q' => 'What is actually at issue in the debate?', 'options' => ['Whether children exist', 'Whether childhood is treated as a distinct institutional stage', 'Whether children work', 'Whether parents love children'], 'correct' => 1],
            ['q' => 'When did specialised institutions largely emerge?', 'options' => ['In antiquity', 'Between the seventeenth and nineteenth centuries', 'After 1950', 'In the Middle Ages'], 'correct' => 1],
            ['q' => 'Which stronger claim has not survived scrutiny?', 'options' => ['That schooling changed', 'That pre-modern societies lacked parental affection', 'That children worked', 'That clothing differed'], 'correct' => 1],
            ['q' => 'What follows if childhood is institutional?', 'options' => ['Its boundaries are fixed', 'Its boundaries are negotiable and have moved', 'It cannot be studied', 'It is identical everywhere'], 'correct' => 1],
            ['q' => 'What does the author advise about current boundaries?', 'options' => ['Treat them as natural', 'Remember they are recent and contested', 'Abolish them', 'Standardise them globally'], 'correct' => 1],
        ],
    ],
    [
        'title' => 'Attention as a Commodity',
        'level' => 'C1',
        'text' => "The observation that attention is scarce is old; the observation that it is therefore a market is more recent and more consequential. Where a service is provided without charge to the user, the user's attention is generally the product being sold, and the design of the service follows from that fact rather than from any intention to inform or entertain.\n\n"
            ."The mechanism is straightforward. Revenue is a function of time spent, so systems are optimised for time spent. Because optimisation is empirical rather than theoretical — variants are tested against millions of users and the better-performing one retained — the resulting design need not have been intended or even understood by anyone. It is selected for, in something close to the biological sense.\n\n"
            ."This explains a pattern that is otherwise puzzling: the tendency of such systems toward material that provokes rather than satisfies. Content that resolves a question ends the session. Content that generates indignation extends it. No designer need have chosen indignation for it to be selected repeatedly.\n\n"
            ."The proposed remedies divide into three families. Regulatory approaches constrain particular techniques, such as autoplay or infinite scroll. Market approaches change the payer, on the argument that a subscriber is a customer while a viewer is inventory. Design approaches attempt to make the cost of attention visible to the user at the moment of spending it.\n\n"
            ."Each addresses a different point in the chain, and the evidence for all three is thin, chiefly because the outcome that matters — whether people are better off — is far harder to measure than the outcome being optimised.",
        'questions' => [
            ['q' => 'What is sold when a service is free to the user?', 'options' => ['The software', 'The user attention', 'Personal data only', 'Nothing'], 'correct' => 1],
            ['q' => 'Why need no one have intended the resulting design?', 'options' => ['It is copied from rivals', 'Variants are tested empirically and the better retained', 'It is legally mandated', 'It is random'], 'correct' => 1],
            ['q' => 'Why does provoking content get selected?', 'options' => ['It is cheaper to produce', 'Resolving content ends the session', 'Users request it', 'It is easier to moderate'], 'correct' => 1],
            ['q' => 'What do market approaches change?', 'options' => ['The interface', 'The payer', 'The content', 'The regulation'], 'correct' => 1],
            ['q' => 'Why is evidence for the remedies thin?', 'options' => ['They are too new', 'Whether people are better off is hard to measure', 'Companies refuse data', 'They are never implemented'], 'correct' => 1],
        ],
    ],
    [
        'title' => 'The Ethics of De-extinction',
        'level' => 'C1',
        'text' => "Proposals to revive extinct species have moved from speculation to funded programmes, and the ethical discussion has not kept pace with the technical one.\n\n"
            ."The first thing to establish is what would actually be produced. Current techniques do not resurrect an extinct animal; they edit the genome of a living relative toward the extinct one. The result is a modified elephant with certain mammoth characteristics, not a mammoth. Whether that distinction matters depends on the purpose, and the purposes offered are not equivalent.\n\n"
            ."The ecological argument holds that certain extinct species performed functions no survivor performs, and that restoring the function would restore a degraded system. This is coherent, and it is also testable: it predicts specific measurable changes, and it can fail. Notably, it does not require the animal to be genetically authentic, only functionally adequate — which weakens the objection about authenticity while raising the question of why a simpler intervention would not serve.\n\n"
            ."The compensatory argument, that humans caused these extinctions and should reverse them, is weaker than it appears. Reversal is not restitution to the individuals harmed, who are long dead, and the resources involved have a clear alternative use in preventing extinctions currently in progress.\n\n"
            ."That opportunity cost is the strongest objection, and it is empirical rather than philosophical. If de-extinction programmes draw funding and attention from conservation of the living, they may produce a net loss of biodiversity while appearing to address it. Whether they do is a question about budgets, and it has not been seriously studied.",
        'questions' => [
            ['q' => 'What do current techniques actually produce?', 'options' => ['An exact extinct animal', 'A modified living relative', 'A clone from fossil DNA', 'A computer model'], 'correct' => 1],
            ['q' => 'What does the ecological argument require?', 'options' => ['Genetic authenticity', 'Functional adequacy', 'Public support', 'Legal protection'], 'correct' => 1],
            ['q' => 'Why is the compensatory argument weak?', 'options' => ['It is unpopular', 'Reversal does not restore the individuals harmed', 'It is illegal', 'It is untestable'], 'correct' => 1],
            ['q' => 'What does the author call the strongest objection?', 'options' => ['Authenticity', 'Animal welfare', 'Opportunity cost', 'Technical difficulty'], 'correct' => 2],
            ['q' => 'What is said about that objection?', 'options' => ['It is philosophical', 'It is empirical and understudied', 'It has been settled', 'It is irrelevant'], 'correct' => 1],
        ],
    ],
    [
        'title' => 'How Scientific Consensus Forms',
        'level' => 'C1',
        'text' => "Public discussion of science frequently treats consensus as though it were a vote, and dissent as though it were evidence of suppression. Neither description survives contact with how consensus actually forms.\n\n"
            ."Agreement in a scientific field is not usually the outcome of persuasion between individuals holding fixed views. It emerges when several independent lines of evidence, gathered by groups with different methods and often different theoretical commitments, converge on the same conclusion. The convergence is what carries the weight, because the errors of one method are unlikely to be the errors of another.\n\n"
            ."This has a consequence for how dissent should be read. A dissenting position that offers an alternative account of all the converging evidence is a serious scientific objection. One that identifies a weakness in a single line while ignoring the others is not, however technically correct the specific criticism may be. Public reporting rarely distinguishes these, since both take the same rhetorical form.\n\n"
            ."The historical cases usually cited against consensus repay closer reading. In most of them, the eventually vindicated minority did not merely disagree; they produced new evidence, and the field changed within a generation of that evidence appearing. The story is often told as vindication of persistence against orthodoxy. It is better read as the ordinary operation of a system that changes its mind when given a reason.\n\n"
            ."What consensus does not do is settle questions of value. That a measure would reduce a harm is a scientific claim; that it should be taken is not, and conflating the two damages the credibility of both.",
        'questions' => [
            ['q' => 'How does consensus mainly emerge?', 'options' => ['By voting', 'By convergence of independent lines of evidence', 'By seniority', 'By funding decisions'], 'correct' => 1],
            ['q' => 'Why does convergence carry weight?', 'options' => ['It involves more people', 'Errors of one method are unlikely to be errors of another', 'It is faster', 'It is cheaper'], 'correct' => 1],
            ['q' => 'Which dissent is a serious objection?', 'options' => ['Any technical criticism', 'One accounting for all the converging evidence', 'One with public support', 'One from a senior figure'], 'correct' => 1],
            ['q' => 'How does the author reread the historical cases?', 'options' => ['As persistence defeating orthodoxy', 'As a system changing when given new evidence', 'As proof consensus is worthless', 'As institutional failure'], 'correct' => 1],
            ['q' => 'What can consensus not settle?', 'options' => ['Empirical questions', 'Questions of value', 'Methodological disputes', 'Measurement problems'], 'correct' => 1],
        ],
    ],
    [
        'title' => 'The Hidden Cost of Convenience',
        'level' => 'C1',
        'text' => "Convenience is rarely examined as a value, being generally treated as an unambiguous good. Yet decisions made in its name have reshaped economies and settlements more thoroughly than most policies deliberately adopted.\n\n"
            ."Consider what convenience displaces. A service that removes an inconvenience usually removes an activity, and activities carry incidental functions that were never their stated purpose. Shopping locally is inefficient as procurement; it is also, incidentally, most of the routine contact many people have with others outside their household. Replacing it with delivery accomplishes the procurement and quietly removes the contact, and no one decided to remove it.\n\n"
            ."This pattern recurs. The convenient option is typically evaluated against the inconvenient one on the dimension both were designed to serve, and the incidental functions appear on neither side of the comparison. They are noticed only in aggregate, years later, and by then the infrastructure supporting the alternative has usually gone.\n\n"
            ."The asymmetry matters. Convenience is experienced individually and immediately; what it displaces is experienced collectively and slowly. An individual choosing rationally at each point will produce, in aggregate, an outcome that the same individuals would not have chosen had it been presented whole.\n\n"
            ."None of this constitutes an argument for inconvenience, which is not a virtue. It is an argument for asking, before a friction is removed, what the friction was doing besides being annoying — a question that is cheap to ask beforehand and often impossible to answer afterwards.",
        'questions' => [
            ['q' => 'What does a convenient service usually remove?', 'options' => ['Only the inconvenience', 'An activity with incidental functions', 'Employment only', 'Nothing significant'], 'correct' => 1],
            ['q' => 'What example does the author give?', 'options' => ['Public transport', 'Local shopping and social contact', 'Online banking', 'Home working'], 'correct' => 1],
            ['q' => 'Why are incidental functions overlooked?', 'options' => ['They are trivial', 'They appear on neither side of the comparison', 'They are secret', 'They are measured wrongly'], 'correct' => 1],
            ['q' => 'What is the asymmetry described?', 'options' => ['Cost versus price', 'Individual and immediate versus collective and slow', 'Urban versus rural', 'Public versus private'], 'correct' => 1],
            ['q' => 'What does the author argue for?', 'options' => ['Embracing inconvenience', 'Asking what a friction was doing before removing it', 'Banning delivery services', 'Ignoring convenience'], 'correct' => 1],
        ],
    ],
    [
        'title' => 'The Standardisation of Time in Sport',
        'level' => 'B2',
        'text' => "Sporting records appear to be straightforward facts: a distance covered in a time, compared across decades. The comparison is considerably less straightforward than it looks, and the reasons illustrate a general problem with measurement over long periods.\n\n"
            ."Timing itself has changed. Hand timing, standard until the 1970s in many sports, systematically produced faster recorded times than electronic timing, because a human operator anticipates the finish and stops the watch marginally early. When electronic timing was introduced, times appeared to worsen, and governing bodies had to decide whether to maintain a single list or separate the eras. Most separated them.\n\n"
            ."Conditions vary in ways that are only partly controlled. Altitude reduces air resistance measurably, which assists sprinting and hinders endurance events. Wind is regulated for sprints but not for all events. Track surfaces have changed composition several times, and each change altered energy return in a direction that was not always documented at the time.\n\n"
            ."Equipment presents the sharpest case. Swimming records set in a particular generation of full-body suits stood conspicuously apart from those before and after, and the sport eventually banned the suits while retaining the records, producing a list with a visible discontinuity that nobody defends as a fair comparison.\n\n"
            ."The general lesson extends well beyond sport. A record is a measurement made under a set of conditions, and comparing measurements across decades requires knowing how those conditions changed. Where the conditions are undocumented, the comparison is not merely imprecise; it is undefined.",
        'questions' => [
            ['q' => 'Why did hand timing produce faster times?', 'options' => ['Watches were inaccurate', 'Operators stopped the watch marginally early', 'Races were shorter', 'Tracks were faster'], 'correct' => 1],
            ['q' => 'What did most governing bodies do about the two eras?', 'options' => ['Merged the lists', 'Separated them', 'Deleted old records', 'Ignored the issue'], 'correct' => 1],
            ['q' => 'How does altitude affect events?', 'options' => ['It helps all events', 'It assists sprinting and hinders endurance', 'It hinders sprinting', 'It has no effect'], 'correct' => 1],
            ['q' => 'What happened with the swimming suits?', 'options' => ['Records were annulled', 'Suits were banned but records retained', 'Both were kept', 'The sport changed distances'], 'correct' => 1],
            ['q' => 'What is the general lesson?', 'options' => ['Records should be abolished', 'Comparison requires knowing how conditions changed', 'Measurement is always precise', 'Old records are better'], 'correct' => 1],
        ],
    ],
];
