<?php

use Illuminate\Database\Seeder;

class MenuTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('menu')->delete();

        \DB::table('menu')->insert([
            0 =>
            [
                'id' => 1000001,
                'href' => 'search',
                'title' => 'Advanced Search',
                'newwindow' => 0,
                'tooltip' => 'Search for releases.',
                'role' => 1,
                'ordinal' => 10,
                'menueval' => '',
            ],
            1 =>
            [
                'id' => 1000002,
                'href' => 'browsegroup',
                'title' => 'Groups List',
                'newwindow' => 0,
                'tooltip' => 'Browse by Group.',
                'role' => 1,
                'ordinal' => 25,
                'menueval' => '',
            ],
            2 =>
            [
                'id' => 1000003,
                'href' => 'movies',
                'title' => 'Movies',
                'newwindow' => 0,
                'tooltip' => 'Browse Movies.',
                'role' => 1,
                'ordinal' => 40,
                'menueval' => '',
            ],
            3 =>
            [
                'id' => 1000004,
                'href' => 'upcoming',
                'title' => 'Theatres',
                'newwindow' => 0,
                'tooltip' => 'Movies currently in theatres.',
                'role' => 1,
                'ordinal' => 45,
                'menueval' => '',
            ],
            4 =>
            [
                'id' => 1000005,
                'href' => 'series',
                'title' => 'TV Series',
                'newwindow' => 0,
                'tooltip' => 'Browse TV Series.',
                'role' => 1,
                'ordinal' => 50,
                'menueval' => '',
            ],
            5 =>
            [
                'id' => 1000006,
                'href' => 'predb',
                'title' => 'PreDB',
                'newwindow' => 0,
                'tooltip' => 'Browse PreDB.',
                'role' => 1,
                'ordinal' => 51,
                'menueval' => '',
            ],
            6 =>
            [
                'id' => 1000007,
                'href' => 'anime',
                'title' => 'Anime',
                'newwindow' => 0,
                'tooltip' => 'Browse Anime',
                'role' => 1,
                'ordinal' => 55,
                'menueval' => '',
            ],
            7 =>
            [
                'id' => 1000008,
                'href' => 'music',
                'title' => 'Music',
                'newwindow' => 0,
                'tooltip' => 'Browse Music.',
                'role' => 1,
                'ordinal' => 60,
                'menueval' => '',
            ],
            8 =>
            [
                'id' => 1000009,
                'href' => 'console',
                'title' => 'Console',
                'newwindow' => 0,
                'tooltip' => 'Browse Games.',
                'role' => 1,
                'ordinal' => 65,
                'menueval' => '',
            ],
            9 =>
            [
                'id' => 1000010,
                'href' => 'books',
                'title' => 'Books',
                'newwindow' => 0,
                'tooltip' => 'Browse Books.',
                'role' => 1,
                'ordinal' => 67,
                'menueval' => '',
            ],
            10 =>
            [
                'id' => 1000011,
                'href' => 'admin',
                'title' => 'Admin',
                'newwindow' => 0,
                'tooltip' => 'Admin',
                'role' => 2,
                'ordinal' => 70,
                'menueval' => '',
            ],
            11 =>
            [
                'id' => 1000012,
                'href' => 'cart',
                'title' => 'My Cart',
                'newwindow' => 0,
                'tooltip' => 'Your Nzb cart.',
                'role' => 1,
                'ordinal' => 75,
                'menueval' => '',
            ],
            12 =>
            [
                'id' => 1000013,
                'href' => 'myshows',
                'title' => 'My Shows',
                'newwindow' => 0,
                'tooltip' => 'Your TV shows.',
                'role' => 1,
                'ordinal' => 77,
                'menueval' => '',
            ],
            13 =>
            [
                'id' => 1000014,
                'href' => 'mymovies',
                'title' => 'My Movies',
                'newwindow' => 0,
                'tooltip' => 'Your Movie Wishlist.',
                'role' => 1,
                'ordinal' => 78,
                'menueval' => '',
            ],
            14 =>
            [
                'id' => 1000015,
                'href' => 'apihelp',
                'title' => 'API',
                'newwindow' => 0,
                'tooltip' => 'Information on the API.',
                'role' => 1,
                'ordinal' => 79,
                'menueval' => '',
            ],
            15 =>
            [
                'id' => 1000016,
                'href' => 'rss',
                'title' => 'RSS',
                'newwindow' => 0,
                'tooltip' => 'RSS Feeds.',
                'role' => 1,
                'ordinal' => 80,
                'menueval' => '',
            ],
            16 =>
            [
                'id' => 1000017,
                'href' => 'queue',
                'title' => 'My Queue',
                'newwindow' => 0,
                'tooltip' => 'View Your Queue.',
                'role' => 1,
                'ordinal' => 0,
                'menueval' => '',
            ],
            17 =>
            [
                'id' => 1000018,
                'href' => 'forum',
                'title' => 'Forum',
                'newwindow' => 0,
                'tooltip' => 'Browse Forum.',
                'role' => 1,
                'ordinal' => 85,
                'menueval' => '',
            ],
            18 =>
            [
                'id' => 1000019,
                'href' => 'login',
                'title' => 'Login',
                'newwindow' => 0,
                'tooltip' => 'Login.',
                'role' => 0,
                'ordinal' => 100,
                'menueval' => '',
            ],
            19 =>
            [
                'id' => 1000020,
                'href' => 'register',
                'title' => 'Register',
                'newwindow' => 0,
                'tooltip' => 'Register.',
                'role' => 0,
                'ordinal' => 110,
                'menueval' => '',
            ],
            20 =>
            [
                'id' => 1000021,
                'href' => 'newposterwall',
                'title' => 'New Releases',
                'newwindow' => 0,
                'tooltip' => 'Newest Releases Poster Wall',
                'role' => 1,
                'ordinal' => 11,
                'menueval' => '',
            ],
            21 =>
            [
                'id' => 1000022,
                'href' => 'content',
                'title' => 'Contents page',
                'newwindow' => 0,
                'tooltip' => 'Administrator messages for users.',
                'role' => 1,
                'ordinal' => 14,
                'menueval' => '',
            ],
        ]);
    }
}
