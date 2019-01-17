<?php

namespace App\Console\Commands\Scraping\Reviews;

use Illuminate\Console\Command;
use App\Console\Commands\Scraping\ScrapingHelper;

class Restorando extends Command
{
    use ScrapingHelper;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraping:reviews_restorando';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to make an scraping for get reviews of the restorando restaurant';

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
            $this->line('Restaurantes: Se terminaron de obtener todos los restaurantes');

            foreach ($restaurants as $index => $restaurant) {

                $this->line('Reviews del Restaurant '. $restaurant['name']);
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
        $page = 0;
        $continue = true;

        while ( $continue ) {

            $link = 'https://lima.restorando.com.pe/restaurantes?page='.++$page;
            $goutte = \Goutte::request('GET',$link);
            $crawler = $goutte->filter('.content div div + ul li');

            if ( $crawler->count() == 0 ) break;

            $restaurants = $crawler->each(function ($node) {

                $name = $node->filter('.title')->text();

                return [
                    'link' => $node->filter('a')->attr('href'),
                    'slug' => str_slug( $name ),
                    'name' => $this->str_clean( $name ),
                    'zone' => $node->filter('.zone')->text(),
                    'cuisine' => $node->filter('.cuisine')->text(),
                    'price' => $node->filter('.price')->text(),
                ];

            });

            $response = array_merge($response,$restaurants);
        }

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
        $page = 0;
        $continue = true;

        while ( $continue ) {

            $link = 'https://lima.restorando.com.pe'.$restaurant['link'].'?review_page='.++$page;
            $goutte = \Goutte::request('GET',$link);
            $crawler = $goutte->filter('#reviews li');

            if ( $crawler->count() == 0 ) break;

            $reviews = $crawler->each(function ($node) use($restaurant){

                $attended = $node->filter('.avatar-cell h5')->attr('content');

                $rating = $node->filter('.avatar-cell span meta')->eq(1)->attr('content');

                return [
                    'slug' => $restaurant['slug'],
                    'restaurant' => $restaurant['name'],
                    'author' => $node->filter('.avatar-cell h4')->text(),
                    'attended' => \Carbon\Carbon::createFromFormat('d/m/Y',$attended),
                    'review' => $node->filter('.review-text p')->text(),
                    'rating' => empty($rating) ? 0 : $rating,
                    'created_at' =>\Carbon\Carbon::now(),
                    'web' => 'Restorando',
                ];

            });

            $response = array_merge($response,$reviews);
        }

        return $response;
    }
}
