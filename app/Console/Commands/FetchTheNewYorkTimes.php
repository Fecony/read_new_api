<?php

namespace App\Console\Commands;

use App\Http\Integrations\News\Requests\TheGuardianRequest;
use App\Http\Integrations\News\Requests\TheNewYorkTimesRequest;
use App\Models\Article;
use Illuminate\Console\Command;

class FetchTheNewYorkTimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-the-new-york-times';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news from The New York Times';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $request = new TheNewYorkTimesRequest;

        $request->query()
            ->add('begin_date', now()->subDays(1)->format('Ymj'));

        $response = $request->send();
        $decodedBody = $response->json();
        $responseBody = collect(data_get($decodedBody, 'response.docs', []));

        $count = count($responseBody);
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $responseBody->each(function ($article) use ($bar) {
            $person = data_get($article, 'byline.person.0');
            $personFullName = data_get($person, 'firstname') . ' ' . data_get($person, 'lastname');
            $author = trim($personFullName) ?: data_get($article, 'byline.original');

            Article::create([
                'title' => data_get($article, 'headline.main'),
                'content' => data_get($article, 'lead_paragraph'),
                'date' => data_get($article, 'pub_date'),
                'category' => data_get($article, 'news_desk'),
                'source' => 'the-new-york-times',
                'author' => $author,
            ]);
            $bar->advance();
        });

        $bar->finish();
        $this->newLine()->info("Created $count articles");

        return self::SUCCESS;
    }
}
