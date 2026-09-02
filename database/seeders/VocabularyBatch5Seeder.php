<?php

namespace Database\Seeders;

use App\Models\Vocabulary\Word;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Fifth batch of the vocabulary expansion (see VocabularyBatch1Seeder).
 * Skips any English word that already exists in the words table
 * (case-insensitive) so the seeder is safe to re-run.
 */
class VocabularyBatch5Seeder extends Seeder
{
    public function run(): void
    {
        $existing = Word::pluck('word')->map(fn ($w) => Str::lower($w))->flip();

        $created = 0;
        $skipped = 0;

        foreach ($this->entries() as $row) {
            [$en, $ipa, $ru, $example, $difficulty, $category] = $row;

            if (isset($existing[Str::lower($en)])) {
                $skipped++;

                continue;
            }

            $word = Word::create([
                'word' => $en,
                'transcription' => $ipa,
                'example' => $example,
                'difficulty' => $difficulty,
                'category' => $category,
            ]);

            $word->translations()->create([
                'language' => 'ru',
                'translation' => $ru,
                'example' => $example,
            ]);

            $existing[Str::lower($en)] = true;
            $created++;
        }

        $this->command?->info("VocabularyBatch5Seeder: +{$created} words, {$skipped} skipped as already existing.");
    }

    /**
     * @return array<int, array{0: string, 1: string, 2: string, 3: string, 4: int, 5: string}>
     */
    private function entries(): array
    {
        $space = 'Космос и астрономия';
        $rel = 'Религия и вера';

        return [
            // ===== Космос и астрономия (Space and Astronomy) =====
            ['space', 'speɪs', 'космос', 'Scientists are studying deep space.', 1, $space],
            ['universe', 'ˈjuːnɪvɜːs', 'вселенная', 'The universe is expanding.', 2, $space],
            ['galaxy', 'ˈɡæləksi', 'галактика', 'Our galaxy is called the Milky Way.', 2, $space],
            ['star', 'stɑː', 'звезда', 'The sky was full of stars.', 1, $space],
            ['planet', 'ˈplænɪt', 'планета', 'Earth is the third planet from the Sun.', 1, $space],
            ['moon', 'muːn', 'луна', 'The moon was full last night.', 1, $space],
            ['sun', 'sʌn', 'солнце', 'The sun rises in the east.', 1, $space],
            ['solar system', 'ˈsəʊlə ˈsɪstəm', 'солнечная система', 'There are eight planets in our solar system.', 2, $space],
            ['orbit', 'ˈɔːbɪt', 'орбита; вращаться по орбите', 'The satellite orbits the Earth every 90 minutes.', 3, $space],
            ['gravity', 'ˈɡrævəti', 'гравитация', 'Gravity keeps us on the ground.', 2, $space],
            ['astronaut', 'ˈæstrənɔːt', 'астронавт', 'The astronaut floated outside the station.', 2, $space],
            ['cosmonaut', 'ˈkɒzmənɔːt', 'космонавт', 'Yuri Gagarin was the first cosmonaut in space.', 3, $space],
            ['spacecraft', 'ˈspeɪskrɑːft', 'космический корабль', 'The spacecraft docked with the station.', 3, $space],
            ['spaceship', 'ˈspeɪsʃɪp', 'космический корабль', 'The spaceship landed safely.', 2, $space],
            ['rocket', 'ˈrɒkɪt', 'ракета', 'The rocket launched into the sky.', 2, $space],
            ['satellite', 'ˈsætəlaɪt', 'спутник', 'The satellite sends weather data to Earth.', 2, $space],
            ['telescope', 'ˈtelɪskəʊp', 'телескоп', 'She looked at the stars through a telescope.', 2, $space],
            ['comet', 'ˈkɒmɪt', 'комета', 'A comet is made of ice and dust.', 3, $space],
            ['asteroid', 'ˈæstərɔɪd', 'астероид', 'An asteroid passed close to Earth.', 3, $space],
            ['meteor', 'ˈmiːtiə', 'метеор', 'We watched a meteor shower last night.', 3, $space],
            ['meteorite', 'ˈmiːtiəraɪt', 'метеорит', 'The meteorite left a crater in the desert.', 4, $space],
            ['black hole', 'blæk həʊl', 'чёрная дыра', 'Nothing can escape a black hole, not even light.', 3, $space],
            ['nebula', 'ˈnebjʊlə', 'туманность', 'The nebula glowed with bright colours.', 4, $space],
            ['constellation', 'ˌkɒnstəˈleɪʃən', 'созвездие', 'Orion is a famous constellation.', 3, $space],
            ['the Milky Way', 'ðə ˈmɪlki weɪ', 'Млечный Путь', 'The Milky Way contains billions of stars.', 3, $space],
            ['Mars', 'mɑːz', 'Марс', 'Scientists want to send humans to Mars.', 1, $space],
            ['Venus', 'ˈviːnəs', 'Венера', 'Venus is the hottest planet in the solar system.', 2, $space],
            ['Jupiter', 'ˈdʒuːpɪtə', 'Юпитер', 'Jupiter is the largest planet.', 2, $space],
            ['Saturn', 'ˈsætən', 'Сатурн', 'Saturn has beautiful rings.', 2, $space],
            ['Mercury', 'ˈmɜːkjəri', 'Меркурий', 'Mercury is closest to the Sun.', 2, $space],
            ['Neptune', 'ˈneptjuːn', 'Нептун', 'Neptune is a cold, blue planet.', 3, $space],
            ['Uranus', 'ˈjʊərənəs', 'Уран', 'Uranus rotates on its side.', 3, $space],
            ['Pluto', 'ˈpluːtəʊ', 'Плутон', 'Pluto is now called a dwarf planet.', 2, $space],
            ['Earth', 'ɜːθ', 'Земля', 'Earth is our home planet.', 1, $space],
            ['eclipse', 'ɪˈklɪps', 'затмение', 'We watched the solar eclipse together.', 3, $space],
            ['solar eclipse', 'ˈsəʊlər ɪˈklɪps', 'солнечное затмение', 'A solar eclipse happens when the moon blocks the sun.', 3, $space],
            ['lunar eclipse', 'ˈluːnər ɪˈklɪps', 'лунное затмение', 'A lunar eclipse turns the moon red.', 3, $space],
            ['launch', 'lɔːntʃ', 'запуск; запускать', 'The launch was delayed due to bad weather.', 2, $space],
            ['landing', 'ˈlændɪŋ', 'посадка', 'The landing on the moon was broadcast worldwide.', 2, $space],
            ['mission', 'ˈmɪʃən', 'миссия', 'The mission lasted six months.', 2, $space],
            ['crew', 'kruː', 'экипаж', 'The crew trained for years before the flight.', 2, $space],
            ['space station', 'speɪs ˈsteɪʃən', 'космическая станция', 'Astronauts live and work on the space station.', 2, $space],
            ['spacewalk', 'ˈspeɪswɔːk', 'выход в открытый космос', 'The spacewalk lasted seven hours.', 4, $space],
            ['astronomy', 'əˈstrɒnəmi', 'астрономия', 'She studies astronomy at university.', 3, $space],
            ['astronomer', 'əˈstrɒnəmə', 'астроном', 'The astronomer discovered a new planet.', 3, $space],
            ['observatory', 'əbˈzɜːvətri', 'обсерватория', 'The observatory is located on a mountain.', 4, $space],
            ['light-year', 'laɪt jɪə', 'световой год', 'The star is four light-years away.', 3, $space],
            ['dwarf planet', 'dwɔːf ˈplænɪt', 'карликовая планета', 'Pluto is classified as a dwarf planet.', 3, $space],
            ['supernova', 'ˌsuːpəˈnəʊvə', 'сверхновая звезда', 'A supernova is an exploding star.', 4, $space],
            ['cosmos', 'ˈkɒzmɒs', 'космос, мироздание', 'The cosmos is vast and mysterious.', 3, $space],
            ['cosmic', 'ˈkɒzmɪk', 'космический', 'Cosmic radiation can be dangerous to astronauts.', 3, $space],
            ['interstellar', 'ˌɪntəˈstelə', 'межзвёздный', 'Interstellar travel is still science fiction.', 4, $space],
            ['extraterrestrial', 'ˌekstrətəˈrestriəl', 'внеземной', 'The film is about extraterrestrial life.', 4, $space],
            ['alien', 'ˈeɪliən', 'инопланетянин; инопланетный', 'The movie is about aliens invading Earth.', 2, $space],
            ['UFO', 'ˌjuː ef ˈəʊ', 'НЛО', 'She claims she saw a UFO last night.', 2, $space],
            ['rover', 'ˈrəʊvə', 'марсоход, планетоход', 'The rover sent back photos from Mars.', 3, $space],
            ['probe', 'prəʊb', 'зонд', 'The space probe reached Jupiter after six years.', 3, $space],
            ['module', 'ˈmɒdjuːl', 'модуль', 'The lunar module landed near the crater.', 3, $space],
            ['capsule', 'ˈkæpsjuːl', 'капсула', 'The capsule splashed down in the ocean.', 3, $space],
            ['booster', 'ˈbuːstə', 'ускоритель, ступень ракеты', 'The booster separated after two minutes.', 4, $space],
            ['fuel', 'ˈfjuːəl', 'топливо', 'The rocket needs a huge amount of fuel.', 2, $space],
            ['weightlessness', 'ˈweɪtləsnəs', 'невесомость', 'Astronauts float because of weightlessness.', 4, $space],
            ['atmosphere', 'ˈætməsfɪə', 'атмосфера', 'The atmosphere protects us from radiation.', 2, $space],
            ['exoplanet', 'ˈeksəʊplænɪt', 'экзопланета', 'Scientists found a new exoplanet.', 4, $space],
            ['dark matter', 'dɑːk ˈmætə', 'тёмная материя', 'Dark matter cannot be seen directly.', 4, $space],
            ['spacesuit', 'ˈspeɪssuːt', 'скафандр', 'The spacesuit protects astronauts in space.', 3, $space],
            ['docking', 'ˈdɒkɪŋ', 'стыковка', 'Docking with the station takes great precision.', 4, $space],
            ['countdown', 'ˈkaʊntdaʊn', 'обратный отсчёт', 'The countdown began ten minutes before launch.', 2, $space],
            ['liftoff', 'ˈlɪftɒf', 'старт (ракеты)', 'Liftoff is scheduled for six in the morning.', 3, $space],
            ['mission control', 'ˈmɪʃən kənˈtrəʊl', 'центр управления полётами', 'Mission control tracked the rocket the whole flight.', 3, $space],
            ['star cluster', 'stɑː ˈklʌstə', 'звёздное скопление', 'A star cluster is a group of stars close together.', 4, $space],
            ['red giant', 'red ˈdʒaɪənt', 'красный гигант', 'The sun will become a red giant one day.', 4, $space],
            ['white dwarf', 'waɪt dwɔːf', 'белый карлик', 'A white dwarf is what remains after a star dies.', 4, $space],
            ['neutron star', 'ˈnjuːtrɒn stɑː', 'нейтронная звезда', 'A neutron star is extremely dense.', 4, $space],
            ['solar wind', 'ˈsəʊlə wɪnd', 'солнечный ветер', 'The solar wind causes the northern lights.', 4, $space],
            ['vacuum', 'ˈvækjuːm', 'вакуум', 'Space is almost a perfect vacuum.', 3, $space],
            ['zero gravity', 'ˈzɪərəʊ ˈɡrævəti', 'невесомость', 'Objects float in zero gravity.', 3, $space],
            ['celestial body', 'sɪˈlestiəl ˈbɒdi', 'небесное тело', 'The moon is the closest celestial body to Earth.', 4, $space],
            ['rotation', 'rəʊˈteɪʃən', 'вращение', 'The Earth completes one rotation every 24 hours.', 3, $space],
            ['axis', 'ˈæksɪs', 'ось', 'The Earth spins on its axis.', 3, $space],
            ['crater', 'ˈkreɪtə', 'кратер', 'The moon is covered with craters.', 2, $space],
            ['surface', 'ˈsɜːfɪs', 'поверхность', 'The rover explored the surface of Mars.', 2, $space],
            ['core', 'kɔː', 'ядро', 'Earth has a hot, molten core.', 2, $space],
            ['magnetic field', 'mæɡˈnetɪk fiːld', 'магнитное поле', 'Earth\'s magnetic field protects us from solar radiation.', 4, $space],
            ['aurora', 'ɔːˈrɔːrə', 'полярное сияние', 'We saw the aurora in the northern sky.', 4, $space],
            ['trajectory', 'trəˈdʒektəri', 'траектория', 'Engineers calculated the rocket\'s trajectory.', 4, $space],
            ['deep space', 'diːp speɪs', 'дальний космос', 'The probe is heading into deep space.', 3, $space],
            ['spaceflight', 'ˈspeɪsflaɪt', 'космический полёт', 'This was her first spaceflight.', 3, $space],
            ['stargazing', 'ˈstɑːɡeɪzɪŋ', 'наблюдение за звёздами', 'We spent the evening stargazing in the desert.', 3, $space],
            ['telescope lens', 'ˈtelɪskəʊp lenz', 'линза телескопа', 'The telescope lens was cleaned carefully.', 3, $space],
            ['space race', 'speɪs reɪs', 'космическая гонка', 'The space race began in the 1950s.', 3, $space],
            ['re-entry', 'riːˈentri', 'вход в атмосферу', 'Re-entry generates enormous heat.', 4, $space],

            // ===== Религия и вера (Religion and Faith) =====
            ['religion', 'rɪˈlɪdʒən', 'религия', 'People practise many different religions around the world.', 2, $rel],
            ['faith', 'feɪθ', 'вера', 'She has strong faith in God.', 2, $rel],
            ['belief', 'bɪˈliːf', 'вера, убеждение', 'His beliefs shaped the way he lived.', 2, $rel],
            ['God', 'ɡɒd', 'Бог', 'Many people pray to God every day.', 1, $rel],
            ['goddess', 'ˈɡɒdes', 'богиня', 'Athena was the Greek goddess of wisdom.', 3, $rel],
            ['prayer', 'preə', 'молитва', 'She said a quiet prayer before the meal.', 2, $rel],
            ['pray', 'preɪ', 'молиться', 'They pray together every morning.', 1, $rel],
            ['worship', 'ˈwɜːʃɪp', 'поклонение; поклоняться', 'People gather here to worship on Sundays.', 3, $rel],
            ['church', 'tʃɜːtʃ', 'церковь', 'They go to church every Sunday.', 1, $rel],
            ['temple', 'ˈtemp(ə)l', 'храм', 'The temple was built over a thousand years ago.', 2, $rel],
            ['mosque', 'mɒsk', 'мечеть', 'Muslims pray five times a day at the mosque.', 2, $rel],
            ['synagogue', 'ˈsɪnəɡɒɡ', 'синагога', 'The synagogue was full during the holiday.', 3, $rel],
            ['priest', 'priːst', 'священник', 'The priest led the Sunday service.', 2, $rel],
            ['pastor', 'ˈpɑːstə', 'пастор', 'The pastor gave an inspiring sermon.', 3, $rel],
            ['monk', 'mʌŋk', 'монах', 'The monk lived quietly in the monastery.', 2, $rel],
            ['nun', 'nʌn', 'монахиня', 'The nun taught at the local school.', 2, $rel],
            ['bishop', 'ˈbɪʃəp', 'епископ', 'The bishop visited every church in the region.', 3, $rel],
            ['pope', 'pəʊp', 'папа римский', 'The Pope addressed thousands of people in the square.', 2, $rel],
            ['rabbi', 'ˈræbaɪ', 'раввин', 'The rabbi explained the meaning of the holiday.', 3, $rel],
            ['imam', 'ɪˈmɑːm', 'имам', 'The imam led the Friday prayers.', 3, $rel],
            ['the Bible', 'ðə ˈbaɪbəl', 'Библия', 'She reads the Bible every night before bed.', 2, $rel],
            ['the Quran', 'ðə kʊˈrɑːn', 'Коран', 'The Quran is the holy book of Islam.', 2, $rel],
            ['the Torah', 'ðə ˈtɔːrə', 'Тора', 'The Torah is central to Jewish faith.', 3, $rel],
            ['scripture', 'ˈskrɪptʃə', 'священное писание', 'He quoted scripture during the sermon.', 3, $rel],
            ['sacred', 'ˈseɪkrɪd', 'священный', 'This is a sacred place for millions of people.', 3, $rel],
            ['holy', 'ˈhəʊli', 'святой, священный', 'Jerusalem is a holy city for three religions.', 2, $rel],
            ['sin', 'sɪn', 'грех', 'Lying is considered a sin in many religions.', 2, $rel],
            ['soul', 'səʊl', 'душа', 'They believe the soul lives on after death.', 2, $rel],
            ['spirit', 'ˈspɪrɪt', 'дух', 'The ceremony honoured the spirits of the ancestors.', 2, $rel],
            ['spiritual', 'ˈspɪrɪtʃuəl', 'духовный', 'She felt a deep spiritual connection during the trip.', 3, $rel],
            ['heaven', 'ˈhevən', 'рай, небеса', 'They believe good people go to heaven.', 2, $rel],
            ['hell', 'hel', 'ад', 'Many religions describe hell as a place of punishment.', 2, $rel],
            ['paradise', 'ˈpærədaɪs', 'рай', 'The garden was described as paradise on Earth.', 2, $rel],
            ['angel', 'ˈeɪndʒəl', 'ангел', 'The story tells of an angel appearing in a dream.', 2, $rel],
            ['devil', 'ˈdev(ə)l', 'дьявол', 'In the story, the devil tempts the hero.', 2, $rel],
            ['demon', 'ˈdiːmən', 'демон', 'The film is about a village haunted by demons.', 3, $rel],
            ['miracle', 'ˈmɪrək(ə)l', 'чудо', 'Everyone called her recovery a miracle.', 2, $rel],
            ['blessing', 'ˈblesɪŋ', 'благословение', 'The priest gave the couple his blessing.', 2, $rel],
            ['bless', 'bles', 'благословлять', 'The elder blessed the newborn child.', 2, $rel],
            ['curse', 'kɜːs', 'проклятие; проклинать', 'The old tale speaks of an ancient curse.', 3, $rel],
            ['ritual', 'ˈrɪtʃuəl', 'ритуал', 'The tribe performs a ritual before the harvest.', 3, $rel],
            ['ceremony', 'ˈserɪməni', 'церемония', 'The wedding ceremony took place in the temple.', 2, $rel],
            ['sermon', 'ˈsɜːmən', 'проповедь', 'The priest gave a moving sermon.', 3, $rel],
            ['hymn', 'hɪm', 'церковный гимн', 'The choir sang a beautiful hymn.', 3, $rel],
            ['chant', 'tʃɑːnt', 'песнопение; петь нараспев', 'The monks began to chant softly.', 3, $rel],
            ['meditation', 'ˌmedɪˈteɪʃən', 'медитация', 'Meditation helps her feel calm and focused.', 2, $rel],
            ['meditate', 'ˈmedɪteɪt', 'медитировать', 'He meditates for twenty minutes every morning.', 2, $rel],
            ['pilgrimage', 'ˈpɪlɡrɪmɪdʒ', 'паломничество', 'Millions of Muslims go on a pilgrimage to Mecca.', 3, $rel],
            ['pilgrim', 'ˈpɪlɡrɪm', 'паломник', 'The pilgrims walked for weeks to reach the shrine.', 3, $rel],
            ['believer', 'bɪˈliːvə', 'верующий', 'Believers gathered at the temple for the festival.', 2, $rel],
            ['atheist', 'ˈeɪθiɪst', 'атеист', 'He describes himself as an atheist.', 3, $rel],
            ['atheism', 'ˈeɪθiɪzəm', 'атеизм', 'Atheism is the lack of belief in any god.', 4, $rel],
            ['agnostic', 'æɡˈnɒstɪk', 'агностик', 'She is agnostic and unsure whether God exists.', 4, $rel],
            ['Christianity', 'ˌkrɪstiˈænəti', 'христианство', 'Christianity is the world\'s largest religion.', 3, $rel],
            ['Christian', 'ˈkrɪstʃən', 'христианин; христианский', 'He grew up in a Christian family.', 2, $rel],
            ['Islam', 'ˈɪzlɑːm', 'ислам', 'Islam is followed by over a billion people.', 2, $rel],
            ['Muslim', 'ˈmʊzlɪm', 'мусульманин; мусульманский', 'She is a practising Muslim.', 2, $rel],
            ['Judaism', 'ˈdʒuːdeɪɪzəm', 'иудаизм', 'Judaism is one of the oldest religions.', 3, $rel],
            ['Jewish', 'ˈdʒuːɪʃ', 'еврейский, иудейский', 'They celebrated a Jewish holiday together.', 2, $rel],
            ['Buddhism', 'ˈbʊdɪzəm', 'буддизм', 'Buddhism teaches the path to enlightenment.', 3, $rel],
            ['Buddhist', 'ˈbʊdɪst', 'буддист; буддийский', 'The Buddhist monk taught us about meditation.', 3, $rel],
            ['Hinduism', 'ˈhɪnduːɪzəm', 'индуизм', 'Hinduism has many gods and goddesses.', 3, $rel],
            ['Hindu', 'ˈhɪnduː', 'индус; индуистский', 'The Hindu festival of lights is called Diwali.', 3, $rel],
            ['monotheism', 'ˈmɒnəʊθiːɪzəm', 'монотеизм', 'Monotheism is the belief in a single god.', 5, $rel],
            ['polytheism', 'ˈpɒlɪθiːɪzəm', 'политеизм', 'Ancient Greek religion was based on polytheism.', 5, $rel],
            ['creation', 'kriˈeɪʃən', 'сотворение мира', 'The story of creation is told in many religions.', 3, $rel],
            ['creator', 'kriˈeɪtə', 'создатель, творец', 'They refer to God as the Creator.', 2, $rel],
            ['commandment', 'kəˈmɑːndmənt', 'заповедь', 'The Ten Commandments are central to Judaism and Christianity.', 4, $rel],
            ['repent', 'rɪˈpent', 'раскаиваться', 'He asked forgiveness and repented for his mistakes.', 4, $rel],
            ['salvation', 'sælˈveɪʃən', 'спасение (души)', 'Christians believe faith leads to salvation.', 4, $rel],
            ['resurrection', 'ˌrezəˈrekʃən', 'воскресение', 'Easter celebrates the resurrection of Jesus.', 4, $rel],
            ['baptism', 'ˈbæptɪzəm', 'крещение', 'The baby\'s baptism took place at the church.', 3, $rel],
            ['baptize', 'bæpˈtaɪz', 'крестить', 'The priest baptized the child on Sunday.', 3, $rel],
            ['confession', 'kənˈfeʃən', 'исповедь', 'He went to confession every week.', 3, $rel],
            ['confess', 'kənˈfes', 'исповедоваться, признаваться', 'She confessed her wrongdoing to the priest.', 3, $rel],
            ['devout', 'dɪˈvaʊt', 'набожный, благочестивый', 'Her grandmother was a devout Catholic.', 4, $rel],
            ['altar', 'ˈɔːltə', 'алтарь', 'The couple stood at the altar during the ceremony.', 3, $rel],
            ['shrine', 'ʃraɪn', 'святыня, храм', 'Pilgrims visit the shrine every year.', 3, $rel],
            ['congregation', 'ˌkɒŋɡrɪˈɡeɪʃən', 'прихожане, паства', 'The congregation sang together at the end of the service.', 4, $rel],
            ['denomination', 'dɪˌnɒmɪˈneɪʃən', 'конфессия', 'There are many denominations within Christianity.', 5, $rel],
            ['sect', 'sekt', 'секта, религиозная группа', 'The small sect had its own unusual customs.', 4, $rel],
            ['doctrine', 'ˈdɒktrɪn', 'доктрина, учение', 'The doctrine has been taught for centuries.', 4, $rel],
            ['theology', 'θiˈɒlədʒi', 'теология, богословие', 'He studied theology at university.', 4, $rel],
            ['divine', 'dɪˈvaɪn', 'божественный', 'They believe the king had divine authority.', 3, $rel],
            ['deity', 'ˈdeɪəti', 'божество', 'Ancient Egyptians worshipped many deities.', 4, $rel],
            ['afterlife', 'ˈɑːftəlaɪf', 'загробная жизнь', 'Many religions teach about life after death, the afterlife.', 3, $rel],
            ['reincarnation', 'ˌriːɪnkɑːˈneɪʃən', 'реинкарнация', 'Hindus and Buddhists believe in reincarnation.', 4, $rel],
            ['karma', 'ˈkɑːmə', 'карма', 'She believes good actions bring good karma.', 3, $rel],
            ['fate', 'feɪt', 'судьба', 'He believed everything happens for a reason, guided by fate.', 3, $rel],
            ['destiny', 'ˈdestɪni', 'судьба, предназначение', 'She felt it was her destiny to become a teacher.', 3, $rel],
            ['superstition', 'ˌsuːpəˈstɪʃən', 'суеверие', 'Breaking a mirror is an old superstition about bad luck.', 3, $rel],
            ['superstitious', 'ˌsuːpəˈstɪʃəs', 'суеверный', 'My grandmother is very superstitious about black cats.', 3, $rel],
            ['tradition', 'trəˈdɪʃən', 'традиция', 'The festival is a tradition passed down for generations.', 2, $rel],
            ['festival', 'ˈfestɪvəl', 'праздник, фестиваль', 'The religious festival lasts three days.', 2, $rel],
            ['Easter', 'ˈiːstə', 'Пасха', 'We visit family every Easter.', 1, $rel],
            ['Christmas', 'ˈkrɪsməs', 'Рождество', 'They decorate the tree every Christmas.', 1, $rel],
            ['Ramadan', 'ˈræmədæn', 'Рамадан', 'Muslims fast during Ramadan.', 3, $rel],
            ['Eid', 'iːd', 'Ид (мусульманский праздник)', 'Families gather to celebrate Eid together.', 3, $rel],
            ['Passover', 'ˈpɑːsəʊvə', 'Песах', 'They hold a special meal during Passover.', 3, $rel],
            ['Diwali', 'dɪˈwɑːli', 'Дивали', 'Diwali is known as the festival of lights.', 3, $rel],
            ['fasting', 'ˈfɑːstɪŋ', 'пост, воздержание от пищи', 'Fasting is practised in many religions.', 3, $rel],
            ['vow', 'vaʊ', 'обет, клятва', 'The monk took a vow of silence.', 3, $rel],
            ['oath', 'əʊθ', 'клятва', 'He swore an oath before the ceremony.', 3, $rel],
            ['sacrifice', 'ˈsækrɪfaɪs', 'жертва; жертвовать', 'Ancient people made sacrifices to their gods.', 3, $rel],
            ['offering', 'ˈɒfərɪŋ', 'подношение', 'Visitors leave a small offering at the shrine.', 3, $rel],
            ['incense', 'ˈɪnsens', 'благовония, ладан', 'The temple smelled of burning incense.', 3, $rel],
            ['candle', 'ˈkænd(ə)l', 'свеча', 'She lit a candle in memory of her grandmother.', 1, $rel],
            ['procession', 'prəˈseʃən', 'процессия, шествие', 'The religious procession moved slowly through the streets.', 4, $rel],
            ['relic', 'ˈrelɪk', 'реликвия', 'The museum displays an ancient religious relic.', 4, $rel],
            ['saint', 'seɪnt', 'святой', 'The church was named after a famous saint.', 2, $rel],
            ['martyr', 'ˈmɑːtə', 'мученик', 'He is remembered as a martyr for his beliefs.', 4, $rel],
            ['disciple', 'dɪˈsaɪpəl', 'ученик, последователь', 'Jesus had twelve disciples.', 3, $rel],
            ['apostle', 'əˈpɒsəl', 'апостол', 'Paul is one of the most famous apostles.', 4, $rel],
            ['prophet', 'ˈprɒfɪt', 'пророк', 'Muhammad is considered a prophet in Islam.', 3, $rel],
            ['gospel', 'ˈɡɒspəl', 'евангелие', 'The gospel tells the story of Jesus\'s life.', 3, $rel],
            ['parable', 'ˈpærəbəl', 'притча', 'The teacher used a parable to explain the lesson.', 4, $rel],
            ['omen', 'ˈəʊmən', 'предзнаменование', 'They saw the storm as a bad omen.', 4, $rel],
            ['monastery', 'ˈmɒnəstri', 'монастырь', 'The monks lived in a quiet monastery in the mountains.', 3, $rel],
            ['convent', 'ˈkɒnvənt', 'женский монастырь', 'She spent her childhood near a convent.', 4, $rel],
        ];
    }
}
