<?php

namespace App\Console\Commands\Scraping\Reviews;

use Illuminate\Console\Command;
use App\Console\Commands\Scraping\ScrapingHelper;

class Mesa247 extends Command
{
    use ScrapingHelper;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraping:reviews_mesa247';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to make an scraping for get reviews of the mesa247 restaurant';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            /**
             * We obtain all the available restaurants from website.
             */
            $restaurants = $this->restaurants();
            $this->line('Success! They finished obtaining all the restaurants');

            foreach ($restaurants as $index => $restaurant) {

                $this->line('Reviews of restaurant '. $restaurant['name']);
                /**
                 * All possible reviews are obtained for each restaurant
                 */
                $reviews = $this->reviews( $restaurant );
                /**
                 * All comments for each restaurant are saved in the table reviews
                 */
                \App\Review::insert($reviews);
                $this->line('Se insertaron los reviews en la base de datos');
            }

        } catch (\Exception $e) {
            $this->error( $e->getMessage() );
        }
        $this->line('Success scraping!');
    }

    /**
     * Scraping to get all the possible restaurants
     * @return array  $response
     */
    protected function restaurants()
    {
        $response = [];

        $link = 'https://www.mesa247.pe/restaurante/listado';
        $goutte = \Goutte::request('GET',$link);
        $crawler = $goutte->filter('#listadoHorariosLocales .borde_boot_list');

        if ( $crawler->count() == 0 ) return [];

        $response = $crawler->each(function ($node) {

            $s = $this->str_clean( $node->filter('.col_name_res label')->text() );
            $s = explode('|',$s);

            $name = $node->filter('.col_name_res .tooltip1_rest')->text();

            return [
                'link' => $node->filter('.col_name_res .tooltip1_rest')->attr('href'),
                'slug' => str_slug( $name ),
                'name' => $this->str_clean( $name ),
                'zone' => trim($s[0]),
                'cuisine' => trim($s[1]),
                'price' => $node->filter('.col_precio_res p')->text(),
            ];

        });

        return $response;
    }

    /**
     * Scraping to get all the reviews of a restaurant
     *
     * @param  array  $restaurant
     * @return array  $response
     */
    protected function reviews( $restaurant )
    {
        $response = [];

        $goutte = \Goutte::request('GET',$restaurant['link']);
        $crawler = $goutte->filter('#tab5 .comment_box');

        if ( $crawler->count() == 0 ) return [];

        $response = $crawler->each(function ($node) use($restaurant){

            $attended = $this->format_date( $node->filter('.comment_stars p')->text() );
            $c1 = $node->filter('.comment_detail .comment_title strong')->text();
            $c2 = $node->filter('.comment_detail .comment_text')->text();
            $rating = $node->filter('.comment_stars meta')->eq(1)->attr('content');
            $rating = empty($rating) ? 0 : $rating;

            return [
                'slug' => $restaurant['slug'],
                'restaurant' => $restaurant['name'],
                'author' => $node->filter('.comment_photo .comment_photo_name')->text(),
                'attended' => \Carbon\Carbon::createFromFormat('d-m-Y',$attended),
                'review' => "$c1. $c2",
                'rating' => $this->standardize_rating($rating,5),
                'created_at' => \Carbon\Carbon::now(),
                'web' => 'Mesa247',
            ];

        });

        return $response;
    }
}
