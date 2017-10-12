<?php

use Illuminate\Database\Seeder;

class GenresTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('genres')->delete();

        \DB::table('genres')->insert([
            0 =>
            [
                'id' => 1,
                'title' => 'Blues',
                'type' => 3000,
                'disabled' => 0,
            ],
            1 =>
            [
                'id' => 2,
                'title' => 'Classic Rock',
                'type' => 3000,
                'disabled' => 0,
            ],
            2 =>
            [
                'id' => 3,
                'title' => 'Country',
                'type' => 3000,
                'disabled' => 0,
            ],
            3 =>
            [
                'id' => 4,
                'title' => 'Dance',
                'type' => 3000,
                'disabled' => 0,
            ],
            4 =>
            [
                'id' => 5,
                'title' => 'Disco',
                'type' => 3000,
                'disabled' => 0,
            ],
            5 =>
            [
                'id' => 6,
                'title' => 'Funk',
                'type' => 3000,
                'disabled' => 0,
            ],
            6 =>
            [
                'id' => 7,
                'title' => 'Grunge',
                'type' => 3000,
                'disabled' => 0,
            ],
            7 =>
            [
                'id' => 8,
                'title' => 'Hip-Hop',
                'type' => 3000,
                'disabled' => 0,
            ],
            8 =>
            [
                'id' => 9,
                'title' => 'Jazz',
                'type' => 3000,
                'disabled' => 0,
            ],
            9 =>
            [
                'id' => 10,
                'title' => 'Metal',
                'type' => 3000,
                'disabled' => 0,
            ],
            10 =>
            [
                'id' => 11,
                'title' => 'New Age',
                'type' => 3000,
                'disabled' => 0,
            ],
            11 =>
            [
                'id' => 12,
                'title' => 'Oldies',
                'type' => 3000,
                'disabled' => 0,
            ],
            12 =>
            [
                'id' => 13,
                'title' => 'Other',
                'type' => 3000,
                'disabled' => 0,
            ],
            13 =>
            [
                'id' => 14,
                'title' => 'Pop',
                'type' => 3000,
                'disabled' => 0,
            ],
            14 =>
            [
                'id' => 15,
                'title' => 'R&B',
                'type' => 3000,
                'disabled' => 0,
            ],
            15 =>
            [
                'id' => 16,
                'title' => 'Rap',
                'type' => 3000,
                'disabled' => 0,
            ],
            16 =>
            [
                'id' => 17,
                'title' => 'Reggae',
                'type' => 3000,
                'disabled' => 0,
            ],
            17 =>
            [
                'id' => 18,
                'title' => 'Rock',
                'type' => 3000,
                'disabled' => 0,
            ],
            18 =>
            [
                'id' => 19,
                'title' => 'Techno',
                'type' => 3000,
                'disabled' => 0,
            ],
            19 =>
            [
                'id' => 20,
                'title' => 'Industrial',
                'type' => 3000,
                'disabled' => 0,
            ],
            20 =>
            [
                'id' => 21,
                'title' => 'Alternative',
                'type' => 3000,
                'disabled' => 0,
            ],
            21 =>
            [
                'id' => 22,
                'title' => 'Ska',
                'type' => 3000,
                'disabled' => 0,
            ],
            22 =>
            [
                'id' => 23,
                'title' => 'Death Metal',
                'type' => 3000,
                'disabled' => 0,
            ],
            23 =>
            [
                'id' => 24,
                'title' => 'Pranks',
                'type' => 3000,
                'disabled' => 0,
            ],
            24 =>
            [
                'id' => 25,
                'title' => 'Soundtrack',
                'type' => 3000,
                'disabled' => 0,
            ],
            25 =>
            [
                'id' => 26,
                'title' => 'Euro-Techno',
                'type' => 3000,
                'disabled' => 0,
            ],
            26 =>
            [
                'id' => 27,
                'title' => 'Ambient',
                'type' => 3000,
                'disabled' => 0,
            ],
            27 =>
            [
                'id' => 28,
                'title' => 'Trip-Hop',
                'type' => 3000,
                'disabled' => 0,
            ],
            28 =>
            [
                'id' => 29,
                'title' => 'Vocal',
                'type' => 3000,
                'disabled' => 0,
            ],
            29 =>
            [
                'id' => 30,
                'title' => 'Jazz+Funk',
                'type' => 3000,
                'disabled' => 0,
            ],
            30 =>
            [
                'id' => 31,
                'title' => 'Fusion',
                'type' => 3000,
                'disabled' => 0,
            ],
            31 =>
            [
                'id' => 32,
                'title' => 'Trance',
                'type' => 3000,
                'disabled' => 0,
            ],
            32 =>
            [
                'id' => 33,
                'title' => 'Classical',
                'type' => 3000,
                'disabled' => 0,
            ],
            33 =>
            [
                'id' => 34,
                'title' => 'Instrumental',
                'type' => 3000,
                'disabled' => 0,
            ],
            34 =>
            [
                'id' => 35,
                'title' => 'Acid',
                'type' => 3000,
                'disabled' => 0,
            ],
            35 =>
            [
                'id' => 36,
                'title' => 'House',
                'type' => 3000,
                'disabled' => 0,
            ],
            36 =>
            [
                'id' => 37,
                'title' => 'Game',
                'type' => 3000,
                'disabled' => 0,
            ],
            37 =>
            [
                'id' => 38,
                'title' => 'Sound Clip',
                'type' => 3000,
                'disabled' => 0,
            ],
            38 =>
            [
                'id' => 39,
                'title' => 'Gospel',
                'type' => 3000,
                'disabled' => 0,
            ],
            39 =>
            [
                'id' => 40,
                'title' => 'Noise',
                'type' => 3000,
                'disabled' => 0,
            ],
            40 =>
            [
                'id' => 41,
                'title' => 'Alternative Rock',
                'type' => 3000,
                'disabled' => 0,
            ],
            41 =>
            [
                'id' => 42,
                'title' => 'Bass',
                'type' => 3000,
                'disabled' => 0,
            ],
            42 =>
            [
                'id' => 43,
                'title' => 'Soul',
                'type' => 3000,
                'disabled' => 0,
            ],
            43 =>
            [
                'id' => 44,
                'title' => 'Punk',
                'type' => 3000,
                'disabled' => 0,
            ],
            44 =>
            [
                'id' => 45,
                'title' => 'Space',
                'type' => 3000,
                'disabled' => 0,
            ],
            45 =>
            [
                'id' => 46,
                'title' => 'Meditative',
                'type' => 3000,
                'disabled' => 0,
            ],
            46 =>
            [
                'id' => 47,
                'title' => 'Instrumental Pop',
                'type' => 3000,
                'disabled' => 0,
            ],
            47 =>
            [
                'id' => 48,
                'title' => 'Instrumental Rock',
                'type' => 3000,
                'disabled' => 0,
            ],
            48 =>
            [
                'id' => 49,
                'title' => 'Ethnic',
                'type' => 3000,
                'disabled' => 0,
            ],
            49 =>
            [
                'id' => 50,
                'title' => 'Gothic',
                'type' => 3000,
                'disabled' => 0,
            ],
            50 =>
            [
                'id' => 51,
                'title' => 'Darkwave',
                'type' => 3000,
                'disabled' => 0,
            ],
            51 =>
            [
                'id' => 52,
                'title' => 'Techno-Industrial',
                'type' => 3000,
                'disabled' => 0,
            ],
            52 =>
            [
                'id' => 53,
                'title' => 'Electronic',
                'type' => 3000,
                'disabled' => 0,
            ],
            53 =>
            [
                'id' => 54,
                'title' => 'Pop-Folk',
                'type' => 3000,
                'disabled' => 0,
            ],
            54 =>
            [
                'id' => 55,
                'title' => 'Eurodance',
                'type' => 3000,
                'disabled' => 0,
            ],
            55 =>
            [
                'id' => 56,
                'title' => 'Dream',
                'type' => 3000,
                'disabled' => 0,
            ],
            56 =>
            [
                'id' => 57,
                'title' => 'Southern Rock',
                'type' => 3000,
                'disabled' => 0,
            ],
            57 =>
            [
                'id' => 58,
                'title' => 'Comedy',
                'type' => 3000,
                'disabled' => 0,
            ],
            58 =>
            [
                'id' => 59,
                'title' => 'Cult',
                'type' => 3000,
                'disabled' => 0,
            ],
            59 =>
            [
                'id' => 60,
                'title' => 'Gangsta',
                'type' => 3000,
                'disabled' => 0,
            ],
            60 =>
            [
                'id' => 61,
                'title' => 'Top 40',
                'type' => 3000,
                'disabled' => 0,
            ],
            61 =>
            [
                'id' => 62,
                'title' => 'Christian Rap',
                'type' => 3000,
                'disabled' => 0,
            ],
            62 =>
            [
                'id' => 63,
                'title' => 'Pop/Funk',
                'type' => 3000,
                'disabled' => 0,
            ],
            63 =>
            [
                'id' => 64,
                'title' => 'Jungle',
                'type' => 3000,
                'disabled' => 0,
            ],
            64 =>
            [
                'id' => 65,
                'title' => 'Native US',
                'type' => 3000,
                'disabled' => 0,
            ],
            65 =>
            [
                'id' => 66,
                'title' => 'Cabaret',
                'type' => 3000,
                'disabled' => 0,
            ],
            66 =>
            [
                'id' => 67,
                'title' => 'New Wave',
                'type' => 3000,
                'disabled' => 0,
            ],
            67 =>
            [
                'id' => 68,
                'title' => 'Psychadelic',
                'type' => 3000,
                'disabled' => 0,
            ],
            68 =>
            [
                'id' => 69,
                'title' => 'Rave',
                'type' => 3000,
                'disabled' => 0,
            ],
            69 =>
            [
                'id' => 70,
                'title' => 'Showtunes',
                'type' => 3000,
                'disabled' => 0,
            ],
            70 =>
            [
                'id' => 71,
                'title' => 'Trailer',
                'type' => 3000,
                'disabled' => 0,
            ],
            71 =>
            [
                'id' => 72,
                'title' => 'Lo-Fi',
                'type' => 3000,
                'disabled' => 0,
            ],
            72 =>
            [
                'id' => 73,
                'title' => 'Tribal',
                'type' => 3000,
                'disabled' => 0,
            ],
            73 =>
            [
                'id' => 74,
                'title' => 'Acid Punk',
                'type' => 3000,
                'disabled' => 0,
            ],
            74 =>
            [
                'id' => 75,
                'title' => 'Acid Jazz',
                'type' => 3000,
                'disabled' => 0,
            ],
            75 =>
            [
                'id' => 76,
                'title' => 'Polka',
                'type' => 3000,
                'disabled' => 0,
            ],
            76 =>
            [
                'id' => 77,
                'title' => 'Retro',
                'type' => 3000,
                'disabled' => 0,
            ],
            77 =>
            [
                'id' => 78,
                'title' => 'Musical',
                'type' => 3000,
                'disabled' => 0,
            ],
            78 =>
            [
                'id' => 79,
                'title' => 'Rock & Roll',
                'type' => 3000,
                'disabled' => 0,
            ],
            79 =>
            [
                'id' => 80,
                'title' => 'Hard Rock',
                'type' => 3000,
                'disabled' => 0,
            ],
            80 =>
            [
                'id' => 81,
                'title' => 'Folk',
                'type' => 3000,
                'disabled' => 0,
            ],
            81 =>
            [
                'id' => 82,
                'title' => 'Folk-Rock',
                'type' => 3000,
                'disabled' => 0,
            ],
            82 =>
            [
                'id' => 83,
                'title' => 'National Folk',
                'type' => 3000,
                'disabled' => 0,
            ],
            83 =>
            [
                'id' => 84,
                'title' => 'Swing',
                'type' => 3000,
                'disabled' => 0,
            ],
            84 =>
            [
                'id' => 85,
                'title' => 'Fast Fusion',
                'type' => 3000,
                'disabled' => 0,
            ],
            85 =>
            [
                'id' => 86,
                'title' => 'Bebob',
                'type' => 3000,
                'disabled' => 0,
            ],
            86 =>
            [
                'id' => 87,
                'title' => 'Latin',
                'type' => 3000,
                'disabled' => 0,
            ],
            87 =>
            [
                'id' => 88,
                'title' => 'Revival',
                'type' => 3000,
                'disabled' => 0,
            ],
            88 =>
            [
                'id' => 89,
                'title' => 'Celtic',
                'type' => 3000,
                'disabled' => 0,
            ],
            89 =>
            [
                'id' => 90,
                'title' => 'Bluegrass',
                'type' => 3000,
                'disabled' => 0,
            ],
            90 =>
            [
                'id' => 91,
                'title' => 'Avantgarde',
                'type' => 3000,
                'disabled' => 0,
            ],
            91 =>
            [
                'id' => 92,
                'title' => 'Gothic Rock',
                'type' => 3000,
                'disabled' => 0,
            ],
            92 =>
            [
                'id' => 93,
                'title' => 'Progressive Rock',
                'type' => 3000,
                'disabled' => 0,
            ],
            93 =>
            [
                'id' => 94,
                'title' => 'Psychedelic Rock',
                'type' => 3000,
                'disabled' => 0,
            ],
            94 =>
            [
                'id' => 95,
                'title' => 'Symphonic Rock',
                'type' => 3000,
                'disabled' => 0,
            ],
            95 =>
            [
                'id' => 96,
                'title' => 'Slow Rock',
                'type' => 3000,
                'disabled' => 0,
            ],
            96 =>
            [
                'id' => 97,
                'title' => 'Big Band',
                'type' => 3000,
                'disabled' => 0,
            ],
            97 =>
            [
                'id' => 98,
                'title' => 'Chorus',
                'type' => 3000,
                'disabled' => 0,
            ],
            98 =>
            [
                'id' => 99,
                'title' => 'Easy Listening',
                'type' => 3000,
                'disabled' => 0,
            ],
            99 =>
            [
                'id' => 100,
                'title' => 'Acoustic',
                'type' => 3000,
                'disabled' => 0,
            ],
            100 =>
            [
                'id' => 101,
                'title' => 'Humour',
                'type' => 3000,
                'disabled' => 0,
            ],
            101 =>
            [
                'id' => 102,
                'title' => 'Speech',
                'type' => 3000,
                'disabled' => 0,
            ],
            102 =>
            [
                'id' => 103,
                'title' => 'Chanson',
                'type' => 3000,
                'disabled' => 0,
            ],
            103 =>
            [
                'id' => 104,
                'title' => 'Opera',
                'type' => 3000,
                'disabled' => 0,
            ],
            104 =>
            [
                'id' => 105,
                'title' => 'Chamber Music',
                'type' => 3000,
                'disabled' => 0,
            ],
            105 =>
            [
                'id' => 106,
                'title' => 'Sonata',
                'type' => 3000,
                'disabled' => 0,
            ],
            106 =>
            [
                'id' => 107,
                'title' => 'Symphony',
                'type' => 3000,
                'disabled' => 0,
            ],
            107 =>
            [
                'id' => 108,
                'title' => 'Booty Bass',
                'type' => 3000,
                'disabled' => 0,
            ],
            108 =>
            [
                'id' => 109,
                'title' => 'Primus',
                'type' => 3000,
                'disabled' => 0,
            ],
            109 =>
            [
                'id' => 110,
                'title' => 'Porn Groove',
                'type' => 3000,
                'disabled' => 0,
            ],
            110 =>
            [
                'id' => 111,
                'title' => 'Satire',
                'type' => 3000,
                'disabled' => 0,
            ],
            111 =>
            [
                'id' => 112,
                'title' => 'Slow Jam',
                'type' => 3000,
                'disabled' => 0,
            ],
            112 =>
            [
                'id' => 113,
                'title' => 'Club',
                'type' => 3000,
                'disabled' => 0,
            ],
            113 =>
            [
                'id' => 114,
                'title' => 'Tango',
                'type' => 3000,
                'disabled' => 0,
            ],
            114 =>
            [
                'id' => 115,
                'title' => 'Samba',
                'type' => 3000,
                'disabled' => 0,
            ],
            115 =>
            [
                'id' => 116,
                'title' => 'Folklore',
                'type' => 3000,
                'disabled' => 0,
            ],
            116 =>
            [
                'id' => 117,
                'title' => 'Ballad',
                'type' => 3000,
                'disabled' => 0,
            ],
            117 =>
            [
                'id' => 118,
                'title' => 'Power Ballad',
                'type' => 3000,
                'disabled' => 0,
            ],
            118 =>
            [
                'id' => 119,
                'title' => 'Rhytmic Soul',
                'type' => 3000,
                'disabled' => 0,
            ],
            119 =>
            [
                'id' => 120,
                'title' => 'Freestyle',
                'type' => 3000,
                'disabled' => 0,
            ],
            120 =>
            [
                'id' => 121,
                'title' => 'Duet',
                'type' => 3000,
                'disabled' => 0,
            ],
            121 =>
            [
                'id' => 122,
                'title' => 'Punk Rock',
                'type' => 3000,
                'disabled' => 0,
            ],
            122 =>
            [
                'id' => 123,
                'title' => 'Drum Solo',
                'type' => 3000,
                'disabled' => 0,
            ],
            123 =>
            [
                'id' => 124,
                'title' => 'Acapella',
                'type' => 3000,
                'disabled' => 0,
            ],
            124 =>
            [
                'id' => 125,
                'title' => 'Euro-House',
                'type' => 3000,
                'disabled' => 0,
            ],
            125 =>
            [
                'id' => 126,
                'title' => 'Dance Hall',
                'type' => 3000,
                'disabled' => 0,
            ],
            126 =>
            [
                'id' => 127,
                'title' => 'Goa',
                'type' => 3000,
                'disabled' => 0,
            ],
            127 =>
            [
                'id' => 128,
                'title' => 'Drum & Bass',
                'type' => 3000,
                'disabled' => 0,
            ],
            128 =>
            [
                'id' => 129,
                'title' => 'Club-House',
                'type' => 3000,
                'disabled' => 0,
            ],
            129 =>
            [
                'id' => 130,
                'title' => 'Hardcore',
                'type' => 3000,
                'disabled' => 0,
            ],
            130 =>
            [
                'id' => 131,
                'title' => 'Terror',
                'type' => 3000,
                'disabled' => 0,
            ],
            131 =>
            [
                'id' => 132,
                'title' => 'Indie',
                'type' => 3000,
                'disabled' => 0,
            ],
            132 =>
            [
                'id' => 133,
                'title' => 'BritPop',
                'type' => 3000,
                'disabled' => 0,
            ],
            133 =>
            [
                'id' => 134,
                'title' => 'Negerpunk',
                'type' => 3000,
                'disabled' => 0,
            ],
            134 =>
            [
                'id' => 135,
                'title' => 'Polsk Punk',
                'type' => 3000,
                'disabled' => 0,
            ],
            135 =>
            [
                'id' => 136,
                'title' => 'Beat',
                'type' => 3000,
                'disabled' => 0,
            ],
            136 =>
            [
                'id' => 137,
                'title' => 'Christian Gangsta',
                'type' => 3000,
                'disabled' => 0,
            ],
            137 =>
            [
                'id' => 138,
                'title' => 'Heavy Metal',
                'type' => 3000,
                'disabled' => 0,
            ],
            138 =>
            [
                'id' => 139,
                'title' => 'Black Metal',
                'type' => 3000,
                'disabled' => 0,
            ],
            139 =>
            [
                'id' => 140,
                'title' => 'Crossover',
                'type' => 3000,
                'disabled' => 0,
            ],
            140 =>
            [
                'id' => 141,
                'title' => 'Contemporary C',
                'type' => 3000,
                'disabled' => 0,
            ],
            141 =>
            [
                'id' => 142,
                'title' => 'Christian Rock',
                'type' => 3000,
                'disabled' => 0,
            ],
            142 =>
            [
                'id' => 143,
                'title' => 'Merengue',
                'type' => 3000,
                'disabled' => 0,
            ],
            143 =>
            [
                'id' => 144,
                'title' => 'Salsa',
                'type' => 3000,
                'disabled' => 0,
            ],
            144 =>
            [
                'id' => 145,
                'title' => 'Thrash Metal',
                'type' => 3000,
                'disabled' => 0,
            ],
            145 =>
            [
                'id' => 146,
                'title' => 'Anime',
                'type' => 3000,
                'disabled' => 0,
            ],
            146 =>
            [
                'id' => 147,
                'title' => 'JPop',
                'type' => 3000,
                'disabled' => 0,
            ],
            147 =>
            [
                'id' => 148,
                'title' => 'SynthPop',
                'type' => 3000,
                'disabled' => 0,
            ],
            148 =>
            [
                'id' => 149,
                'title' => 'Electronica',
                'type' => 3000,
                'disabled' => 0,
            ],
        ]);
    }
}
