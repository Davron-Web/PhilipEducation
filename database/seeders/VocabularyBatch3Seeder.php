<?php

namespace Database\Seeders;

use App\Models\Vocabulary\Word;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Third batch of the vocabulary expansion (see VocabularyBatch1Seeder).
 * Skips any English word that already exists in the words table
 * (case-insensitive) so the seeder is safe to re-run.
 */
class VocabularyBatch3Seeder extends Seeder
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

        $this->command?->info("VocabularyBatch3Seeder: +{$created} words, {$skipped} skipped as already existing.");
    }

    /**
     * @return array<int, array{0: string, 1: string, 2: string, 3: string, 4: int, 5: string}>
     */
    private function entries(): array
    {
        $music = 'Музыка';
        $film = 'Кино и телевидение';

        return [
            // ===== Музыка (Music) =====
            ['melody', 'ˈmelədi', 'мелодия', 'The song has a beautiful melody.', 3, $music],
            ['rhythm', 'ˈrɪðəm', 'ритм', 'The drums keep the rhythm steady.', 4, $music],
            ['harmony', 'ˈhɑːməni', 'гармония', 'The singers blended in perfect harmony.', 5, $music],
            ['tempo', 'ˈtempəʊ', 'темп', 'The song has a fast tempo.', 4, $music],
            ['beat', 'biːt', 'бит', 'I love the beat of this song.', 2, $music],
            ['note', 'nəʊt', 'нота', 'She hit every note perfectly.', 3, $music],
            ['chord', 'kɔːd', 'аккорд', 'He played a simple chord on the guitar.', 4, $music],
            ['scale', 'skeɪl', 'гамма (муз.)', 'Beginners practise scales every day.', 4, $music],
            ['tune', 'tjuːn', 'мелодия/мотив', 'I can\'t get that tune out of my head.', 2, $music],
            ['lyrics', 'ˈlɪrɪks', 'текст песни', 'The lyrics of this song are very touching.', 3, $music],
            ['verse', 'vɜːs', 'куплет', 'The first verse describes a summer day.', 4, $music],
            ['chorus', 'ˈkɔːrəs', 'припев', 'Everyone sang along to the chorus.', 3, $music],
            ['violin', 'ˌvaɪəˈlɪn', 'скрипка', 'She has played the violin since she was five.', 2, $music],
            ['drums', 'drʌmz', 'ударные', 'He plays drums in a rock band.', 2, $music],
            ['trumpet', 'ˈtrʌmpɪt', 'труба', 'The trumpet sounded loud and clear.', 4, $music],
            ['saxophone', 'ˈsæksəfəʊn', 'саксофон', 'He plays the saxophone beautifully.', 4, $music],
            ['flute', 'fluːt', 'флейта', 'She learned to play the flute at school.', 3, $music],
            ['cello', 'ˈtʃeləʊ', 'виолончель', 'The cello has a deep, rich sound.', 4, $music],
            ['bass', 'beɪs', 'бас', 'He plays bass in the band.', 3, $music],
            ['drum', 'drʌm', 'барабан', 'He hit the drum with the stick.', 2, $music],
            ['orchestra', 'ˈɔːkɪstrə', 'оркестр', 'The orchestra performed a symphony.', 4, $music],
            ['band', 'bænd', 'группа (музыкальная)', 'They formed a band in high school.', 2, $music],
            ['choir', 'ˈkwaɪə', 'хор', 'She sings in the church choir.', 4, $music],
            ['singer', 'ˈsɪŋə', 'певец', 'The singer performed her latest hit.', 2, $music],
            ['vocalist', 'ˈvəʊkəlɪst', 'вокалист', 'The band is looking for a new vocalist.', 5, $music],
            ['composer', 'kəmˈpəʊzə', 'композитор', 'Beethoven is a famous composer.', 4, $music],
            ['musician', 'mjuːˈzɪʃən', 'музыкант', 'She has been a professional musician for years.', 3, $music],
            ['audience', 'ˈɔːdiəns', 'публика', 'The audience clapped loudly after the show.', 3, $music],
            ['concert', 'ˈkɒnsət', 'концерт', 'We went to a concert last night.', 2, $music],
            ['stage', 'steɪdʒ', 'сцена', 'The singer walked onto the stage.', 2, $music],
            ['microphone', 'ˈmaɪkrəfəʊn', 'микрофон', 'She sang into the microphone.', 3, $music],
            ['amplifier', 'ˈæmplɪfaɪə', 'усилитель', 'The guitar was connected to an amplifier.', 5, $music],
            ['speaker', 'ˈspiːkə', 'колонка', 'Turn up the volume on the speaker.', 2, $music],
            ['headphones', 'ˈhedfəʊnz', 'наушники', 'I listen to music with my headphones on.', 2, $music],
            ['playlist', 'ˈpleɪlɪst', 'плейлист', 'She made a playlist for the party.', 3, $music],
            ['album', 'ˈælbəm', 'альбом', 'The band released a new album this year.', 3, $music],
            ['track', 'træk', 'трек (песня)', 'This is my favourite track on the album.', 3, $music],
            ['single', 'ˈsɪŋɡəl', 'сингл', 'The singer released a new single.', 4, $music],
            ['genre', 'ˈʒɒnrə', 'жанр', 'What genre of music do you like?', 4, $music],
            ['pop', 'pɒp', 'поп-музыка', 'She mostly listens to pop music.', 2, $music],
            ['rock', 'rɒk', 'рок', 'He grew up listening to rock music.', 2, $music],
            ['jazz', 'dʒæz', 'джаз', 'They played jazz at the café.', 3, $music],
            ['classical', 'ˈklæsɪkəl', 'классический (о музыке)', 'She prefers classical music to pop.', 3, $music],
            ['hip-hop', 'ˈhɪp hɒp', 'хип-хоп', 'Hip-hop is very popular among teenagers.', 3, $music],
            ['reggae', 'ˈreɡeɪ', 'регги', 'Reggae music originated in Jamaica.', 4, $music],
            ['folk', 'fəʊk', 'фолк', 'She sings traditional folk songs.', 4, $music],
            ['opera', 'ˈɒpərə', 'опера', 'We watched an opera at the theatre.', 4, $music],
            ['symphony', 'ˈsɪmfəni', 'симфония', 'The orchestra performed a famous symphony.', 5, $music],
            ['sonata', 'səˈnɑːtə', 'соната', 'She played a piano sonata beautifully.', 6, $music],
            ['ballad', 'ˈbæləd', 'баллада', 'He wrote a romantic ballad for her.', 5, $music],
            ['anthem', 'ˈænθəm', 'гимн', 'They sang the national anthem before the match.', 4, $music],
            ['soundtrack', 'ˈsaʊndtræk', 'саундтрек', 'The film\'s soundtrack was amazing.', 4, $music],
            ['cover', 'ˈkʌvə', 'кавер-версия', 'She recorded a cover of a classic song.', 4, $music],
            ['remix', 'ˈriːmɪks', 'ремикс', 'The DJ played a remix of the original song.', 4, $music],
            ['acoustic', 'əˈkuːstɪk', 'акустический', 'He performed an acoustic version of the song.', 5, $music],
            ['octave', 'ˈɒktɪv', 'октава', 'She can sing two octaves.', 6, $music],
            ['duet', 'djuˈet', 'дуэт', 'They sang a duet together.', 4, $music],
            ['solo', 'ˈsəʊləʊ', 'соло', 'He played a guitar solo during the concert.', 3, $music],
            ['ensemble', 'ɒnˈsɒmbəl', 'ансамбль', 'The music ensemble performed at the festival.', 6, $music],
            ['rehearsal', 'rɪˈhɜːsəl', 'репетиция', 'The band has rehearsal every Tuesday.', 4, $music],
            ['audition', 'ɔːˈdɪʃən', 'прослушивание', 'She went to an audition for the choir.', 5, $music],
            ['tour', 'tʊə', 'тур (гастроли)', 'The band is going on tour next month.', 3, $music],
            ['gig', 'ɡɪɡ', 'выступление (концерт)', 'They played a small gig at the local pub.', 4, $music],
            ['festival', 'ˈfestɪvəl', 'фестиваль', 'We went to a music festival last summer.', 3, $music],
            ['royalty', 'ˈrɔɪəlti', 'роялти', 'Musicians earn royalties from streaming.', 6, $music],
            ['sample', 'ˈsɑːmpəl', 'сэмпл', 'The producer used a sample from an old song.', 5, $music],
            ['DJ', 'ˌdiːˈdʒeɪ', 'диджей', 'The DJ played music all night.', 2, $music],
            ['turntable', 'ˈtɜːnteɪbəl', 'проигрыватель', 'He collects vinyl records for his turntable.', 5, $music],
            ['vinyl', 'ˈvaɪnəl', 'винил (пластинка)', 'She still buys music on vinyl.', 4, $music],
            ['cassette', 'kəˈset', 'кассета', 'My parents used to listen to cassettes.', 4, $music],
            ['karaoke', 'ˌkæriˈəʊki', 'караоке', 'We sang karaoke all night.', 3, $music],
            ['hum', 'hʌm', 'напевать', 'She hummed a tune while cooking.', 3, $music],
            ['whistle', 'ˈwɪsəl', 'свистеть', 'He whistled a happy tune.', 3, $music],
            ['chant', 'tʃɑːnt', 'скандировать', 'The fans chanted the team\'s name.', 5, $music],
            ['lullaby', 'ˈlʌləbaɪ', 'колыбельная', 'She sang a lullaby to her baby.', 4, $music],
            ['melodic', 'məˈlɒdɪk', 'мелодичный', 'The song has a very melodic chorus.', 6, $music],
            ['vocal', 'ˈvəʊkəl', 'вокальный', 'Her vocal range is impressive.', 4, $music],
            ['backing vocals', 'ˈbækɪŋ ˈvəʊkəlz', 'бэк-вокал', 'She sings backing vocals for the band.', 6, $music],
            ['chart', 'tʃɑːt', 'чарт (хит-парад)', 'The song reached number one on the charts.', 4, $music],
            ['hit', 'hɪt', 'хит', 'This song was a huge hit last summer.', 2, $music],
            ['tribute band', 'ˈtrɪbjuːt bænd', 'трибьют-группа', 'The tribute band played all the classic songs.', 6, $music],
            ['busker', 'ˈbʌskə', 'уличный музыкант', 'A busker was playing guitar in the square.', 6, $music],
            ['jam session', 'dʒæm ˈseʃən', 'джем-сейшн', 'The musicians had a jam session after the show.', 6, $music],
            ['improvise', 'ˈɪmprəvaɪz', 'импровизировать', 'Jazz musicians often improvise during a solo.', 6, $music],
            ['compose', 'kəmˈpəʊz', 'сочинять (музыку)', 'She composed the music for the film.', 5, $music],
            ['soundcheck', 'ˈsaʊndtʃek', 'саундчек', 'The band did a soundcheck before the concert.', 5, $music],
            ['encore', 'ˈɒŋkɔː', 'бис', 'The crowd shouted for an encore.', 5, $music],
            ['applause', 'əˈplɔːz', 'аплодисменты', 'The performance ended with loud applause.', 4, $music],
            ['standing ovation', 'ˈstændɪŋ əʊˈveɪʃən', 'овация стоя', 'The audience gave the singer a standing ovation.', 6, $music],
            ['fan', 'fæn', 'фанат', 'She is a huge fan of that band.', 2, $music],
            ['setlist', 'ˈsetlɪst', 'сет-лист', 'The band changed their setlist for the tour.', 6, $music],
            ['backstage', 'ˌbækˈsteɪdʒ', 'закулисье', 'We got to go backstage after the show.', 4, $music],
            ['subwoofer', 'ˈsʌbwuːfə', 'сабвуфер', 'The subwoofer makes the bass sound powerful.', 6, $music],
            ['treble', 'ˈtrebəl', 'высокие частоты', 'Turn down the treble on the speaker.', 6, $music],
            ['bass line', 'beɪs laɪn', 'басовая партия', 'The bass line drives the whole song.', 6, $music],
            ['riff', 'rɪf', 'рифф', 'The guitar riff is instantly recognisable.', 6, $music],
            ['intro', 'ˈɪntrəʊ', 'вступление', 'The song has a slow, quiet intro.', 3, $music],
            ['outro', 'ˈaʊtrəʊ', 'концовка (песни)', 'The outro fades out slowly.', 5, $music],
            ['a cappella', 'ˌɑː kəˈpelə', 'а капелла', 'The group sang a cappella, without instruments.', 6, $music],
            ['sheet music', 'ʃiːt ˈmjuːzɪk', 'ноты', 'She learned to read sheet music as a child.', 5, $music],
            ['staff', 'stɑːf', 'нотный стан', 'Notes are written on a musical staff.', 6, $music],
            ['clef', 'klef', 'ключ (нотный)', 'The treble clef is used for higher notes.', 7, $music],
            ['time signature', 'taɪm ˈsɪɡnətʃə', 'размер (муз.)', 'The song is written in 4/4 time signature.', 7, $music],
            ['key signature', 'kiː ˈsɪɡnətʃə', 'ключевые знаки', 'The key signature shows which notes are sharp.', 7, $music],
            ['interval', 'ˈɪntəvəl', 'интервал (муз.)', 'The interval between the notes creates tension.', 7, $music],
            ['cadence', 'ˈkeɪdəns', 'каденция', 'The song ends with a strong cadence.', 7, $music],
            ['metronome', 'ˈmetrənəʊm', 'метроном', 'She practises with a metronome to keep time.', 6, $music],
            ['reggaeton', 'ˌreɡeɪˈtɒn', 'реггетон', 'Reggaeton is very popular in Latin America.', 5, $music],
            ['k-pop', 'ˈkeɪ pɒp', 'к-поп', 'K-pop has millions of fans worldwide.', 3, $music],
            ['indie', 'ˈɪndi', 'инди (музыка)', 'She listens mostly to indie bands.', 4, $music],
            ['punk', 'pʌŋk', 'панк', 'Punk music became popular in the 1970s.', 4, $music],
            ['metal', 'ˈmetəl', 'метал (музыка)', 'He is a big fan of heavy metal.', 3, $music],
            ['blues', 'bluːz', 'блюз', 'Blues music expresses deep emotion.', 3, $music],
            ['instrumental', 'ˌɪnstrəˈmentəl', 'инструментальный', 'The album has three instrumental tracks.', 6, $music],
            ['unplugged', 'ʌnˈplʌɡd', 'акустический (без электро)', 'The band played an unplugged set.', 5, $music],
            ['sing-along', 'ˈsɪŋ əlɒŋ', 'песня для подпевания', 'The concert ended with a fun sing-along.', 5, $music],
            ['tuning', 'ˈtjuːnɪŋ', 'настройка (инструмента)', 'He spent a few minutes tuning his guitar.', 5, $music],
            ['perfect pitch', 'ˈpɜːfɪkt pɪtʃ', 'абсолютный слух', 'She has perfect pitch and can name any note.', 7, $music],
            ['tone-deaf', 'təʊn def', 'лишённый слуха', 'He admits he\'s completely tone-deaf.', 6, $music],
            ['earworm', 'ˈɪəwɜːm', 'навязчивая мелодия', 'That song is such an earworm!', 6, $music],
            ['jingle', 'ˈdʒɪŋɡəl', 'джингл (реклама)', 'The advert had a catchy jingle.', 6, $music],

            // ===== Кино и телевидение (Film and Television) =====
            ['director', 'daɪˈrektə', 'режиссёр', 'The director won an award for the film.', 3, $film],
            ['producer', 'prəˈdjuːsə', 'продюсер', 'The producer funded the entire project.', 4, $film],
            ['screenwriter', 'ˈskriːnraɪtə', 'сценарист', 'The screenwriter wrote the script in six months.', 5, $film],
            ['script', 'skrɪpt', 'сценарий', 'The actors learned their lines from the script.', 3, $film],
            ['screenplay', 'ˈskriːnpleɪ', 'киносценарий', 'She wrote the screenplay for the movie.', 5, $film],
            ['scene', 'siːn', 'сцена (в фильме)', 'That was a very emotional scene.', 3, $film],
            ['shot', 'ʃɒt', 'кадр', 'The opening shot of the film was stunning.', 4, $film],
            ['camera', 'ˈkæmərə', 'камера', 'The camera followed the actor closely.', 2, $film],
            ['cinematography', 'ˌsɪnəməˈtɒɡrəfi', 'операторская работа', 'The film won an award for its cinematography.', 7, $film],
            ['editing', 'ˈedɪtɪŋ', 'монтаж', 'The editing made the action scenes exciting.', 4, $film],
            ['actor', 'ˈæktə', 'актёр', 'The actor delivered a powerful performance.', 2, $film],
            ['actress', 'ˈæktrəs', 'актриса', 'The actress won several awards.', 2, $film],
            ['cast', 'kɑːst', 'актёрский состав', 'The film has a very talented cast.', 3, $film],
            ['character', 'ˈkærəktə', 'персонаж', 'The main character faces a difficult choice.', 3, $film],
            ['plot', 'plɒt', 'сюжет', 'The plot was full of surprising twists.', 3, $film],
            ['storyline', 'ˈstɔːrilaɪn', 'сюжетная линия', 'The storyline follows two brothers.', 4, $film],
            ['sequel', 'ˈsiːkwəl', 'сиквел', 'The sequel was even better than the original.', 4, $film],
            ['prequel', 'ˈpriːkwəl', 'приквел', 'The prequel explains how it all began.', 5, $film],
            ['trilogy', 'ˈtrɪlədʒi', 'трилогия', 'The trilogy became extremely popular.', 5, $film],
            ['premiere', 'ˈpremieə', 'премьера', 'The film\'s premiere was held in Los Angeles.', 4, $film],
            ['box office', 'bɒks ˈɒfɪs', 'кассовые сборы', 'The film was a huge success at the box office.', 5, $film],
            ['blockbuster', 'ˈblɒkbʌstə', 'блокбастер', 'This summer\'s blockbuster broke all records.', 5, $film],
            ['review', 'rɪˈvjuː', 'рецензия', 'The film received positive reviews.', 3, $film],
            ['critic', 'ˈkrɪtɪk', 'критик', 'The critic praised the director\'s vision.', 4, $film],
            ['rating', 'ˈreɪtɪŋ', 'рейтинг', 'The film has a high rating online.', 3, $film],
            ['subtitle', 'ˈsʌbtaɪtəl', 'субтитры', 'I watched the film with subtitles.', 3, $film],
            ['dub', 'dʌb', 'дублировать', 'The film was dubbed into several languages.', 5, $film],
            ['dubbing', 'ˈdʌbɪŋ', 'дубляж', 'The dubbing didn\'t match the actor\'s lips well.', 6, $film],
            ['animation', 'ˌænɪˈmeɪʃən', 'анимация', 'The animation in this film is beautiful.', 4, $film],
            ['animated', 'ˈænɪmeɪtɪd', 'анимационный', 'Children love animated films.', 3, $film],
            ['thriller', 'ˈθrɪlə', 'триллер', 'The thriller kept me on the edge of my seat.', 3, $film],
            ['horror', 'ˈhɒrə', 'фильм ужасов', 'I don\'t like watching horror films.', 3, $film],
            ['comedy', 'ˈkɒmədi', 'комедия', 'We watched a funny comedy last night.', 2, $film],
            ['drama', 'ˈdrɑːmə', 'драма', 'The drama moved the audience to tears.', 2, $film],
            ['romance', 'rəʊˈmæns', 'романтический фильм', 'She loves watching romance films.', 3, $film],
            ['sci-fi', 'ˈsaɪ faɪ', 'научная фантастика', 'He is a huge fan of sci-fi films.', 3, $film],
            ['fantasy', 'ˈfæntəsi', 'фэнтези', 'The fantasy world in the film was incredible.', 3, $film],
            ['mystery', 'ˈmɪstəri', 'детектив (жанр)', 'The mystery kept everyone guessing.', 3, $film],
            ['trailer', 'ˈtreɪlə', 'трейлер', 'The trailer made the film look amazing.', 3, $film],
            ['teaser', 'ˈtiːzə', 'тизер', 'The studio released a short teaser online.', 4, $film],
            ['episode', 'ˈepɪsəʊd', 'эпизод', 'I watched three episodes in one night.', 3, $film],
            ['series', 'ˈsɪəriːz', 'сериал', 'This series has five seasons.', 3, $film],
            ['sitcom', 'ˈsɪtkɒm', 'ситком', 'It\'s a classic 90s sitcom.', 4, $film],
            ['rerun', 'ˈriːrʌn', 'повтор (передачи)', 'They showed a rerun of an old episode.', 5, $film],
            ['cliffhanger', 'ˈklɪfhæŋə', 'клиффхэнгер', 'The episode ended on a huge cliffhanger.', 6, $film],
            ['spin-off', 'ˈspɪn ɒf', 'спин-офф', 'The show got its own spin-off series.', 6, $film],
            ['remake', 'ˈriːmeɪk', 'ремейк', 'The remake wasn\'t as good as the original.', 4, $film],
            ['special effects', 'ˈspeʃəl ɪˈfekts', 'спецэффекты', 'The film used impressive special effects.', 4, $film],
            ['CGI', 'ˌsiːdʒiːˈaɪ', 'компьютерная графика', 'The dragon was created using CGI.', 5, $film],
            ['stunt', 'stʌnt', 'трюк', 'The actor performed his own stunts.', 4, $film],
            ['stuntman', 'ˈstʌntmæn', 'каскадёр', 'The stuntman jumped from the building.', 4, $film],
            ['costume', 'ˈkɒstjuːm', 'костюм (актёра)', 'The costumes in this film are stunning.', 3, $film],
            ['makeup', 'ˈmeɪkʌp', 'грим', 'The makeup made the actor look much older.', 3, $film],
            ['studio', 'ˈstjuːdiəʊ', 'киностудия', 'The film was made at a famous studio.', 3, $film],
            ['screening', 'ˈskriːnɪŋ', 'кинопоказ', 'We attended an early screening of the film.', 4, $film],
            ['nomination', 'ˌnɒmɪˈneɪʃən', 'номинация', 'The film received five award nominations.', 5, $film],
            ['oscar', 'ˈɒskə', 'Оскар', 'She won an Oscar for best actress.', 3, $film],
            ['viewer', 'ˈvjuːə', 'зритель', 'Millions of viewers watched the finale.', 3, $film],
            ['voiceover', 'ˈvɔɪsəʊvə', 'закадровый голос', 'The documentary uses a calm voiceover.', 5, $film],
            ['credits', 'ˈkredɪts', 'титры', 'Stay until the end of the credits for a bonus scene.', 3, $film],
            ['cameo', 'ˈkæmiəʊ', 'камео', 'The director made a cameo appearance in the film.', 6, $film],
            ['flashback', 'ˈflæʃbæk', 'флешбэк', 'The film uses flashbacks to explain the past.', 5, $film],
            ['plot twist', 'plɒt twɪst', 'сюжетный поворот', 'The plot twist at the end shocked everyone.', 5, $film],
            ['antagonist', 'ænˈtæɡənɪst', 'антагонист', 'The antagonist is more interesting than the hero.', 6, $film],
            ['villain', 'ˈvɪlən', 'злодей', 'The villain has a very clear motive.', 3, $film],
            ['hero', 'ˈhɪərəʊ', 'герой', 'The hero saves the city in the end.', 2, $film],
            ['supporting actor', 'səˈpɔːtɪŋ ˈæktə', 'актёр второго плана', 'He won an award for best supporting actor.', 5, $film],
            ['extra', 'ˈekstrə', 'массовка', 'She worked as an extra in the crowd scene.', 4, $film],
            ['casting', 'ˈkɑːstɪŋ', 'кастинг', 'The casting for the lead role took months.', 4, $film],
            ['take', 'teɪk', 'дубль', 'They needed ten takes to get the scene right.', 3, $film],
            ['retake', 'ˈriːteɪk', 'пересъёмка', 'The director asked for a retake of the scene.', 5, $film],
            ['close-up', 'ˈkləʊs ʌp', 'крупный план', 'The close-up showed her tears clearly.', 4, $film],
            ['wide shot', 'waɪd ʃɒt', 'общий план', 'The wide shot revealed the whole city.', 5, $film],
            ['angle', 'ˈæŋɡəl', 'ракурс', 'The camera angle made the scene look dramatic.', 4, $film],
            ['frame', 'freɪm', 'кадр (изображение)', 'Every frame of this film looks like a painting.', 4, $film],
            ['montage', 'ˈmɒntɑːʒ', 'монтажная нарезка', 'The training montage is a classic film technique.', 6, $film],
            ['fade', 'feɪd', 'затемнение', 'The scene ends with a slow fade to black.', 4, $film],
            ['cut', 'kʌt', 'монтажная склейка', 'The director shouted "cut" after the scene.', 3, $film],
            ['dissolve', 'dɪˈzɒlv', 'плавный переход', 'The editor used a dissolve between the two scenes.', 6, $film],
            ['soundstage', 'ˈsaʊndsteɪdʒ', 'съёмочный павильон', 'The film was shot entirely on a soundstage.', 6, $film],
            ['props', 'prɒps', 'реквизит', 'The props on set looked very realistic.', 4, $film],
            ['prop', 'prɒp', 'предмет реквизита', 'The sword was just a plastic prop.', 4, $film],
            ['set design', 'set dɪˈzaɪn', 'декорации', 'The set design recreated an entire medieval town.', 5, $film],
            ['location', 'ləʊˈkeɪʃən', 'локация (съёмок)', 'The film was shot on location in Italy.', 3, $film],
            ['filming', 'ˈfɪlmɪŋ', 'съёмки', 'Filming began early in the morning.', 3, $film],
            ['shoot', 'ʃuːt', 'снимать (фильм)', 'They will shoot the final scene tomorrow.', 3, $film],
            ['wrap', 'ræp', 'завершение съёмок', 'The crew celebrated after the final wrap.', 5, $film],
            ['flop', 'flɒp', 'провал (фильма)', 'The expensive film turned out to be a flop.', 5, $film],
            ['age rating', 'eɪdʒ ˈreɪtɪŋ', 'возрастной рейтинг', 'The film has a strict age rating.', 5, $film],
            ['viewership', 'ˈvjuːəʃɪp', 'зрительская аудитория', 'The show\'s viewership grew every season.', 6, $film],
            ['miniseries', 'ˈmɪnisɪəriːz', 'мини-сериал', 'The story was told in a four-part miniseries.', 5, $film],
            ['crossover', 'ˈkrɒsəʊvə', 'кроссовер (эпизод)', 'The two shows had a special crossover episode.', 6, $film],
        ];
    }
}
