<?php

namespace App\Console\Commands;

use App\Http\Integrations\News\Requests\NewsApiRequest;
use App\Models\Article;
use Illuminate\Console\Command;

class FetchNewsApi extends Command
{
    private const PER_PAGE_LIMIT = 10;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-news-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch NewsAPI provider';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $request = new NewsApiRequest;

        $request->query()
            ->add('from', now()->subDays(1)->format('Y-m-j'))
            ->add('pageSize', self::PER_PAGE_LIMIT)
            ->add('language', 'en');

        $response = $request->send();
        $decodedBody = $response->json();
        $responseBody = collect(data_get($decodedBody, 'articles', []));

        $count = count($responseBody);
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $responseBody->each(function ($article) use ($bar) {
            Article::create([
                'title' => data_get($article, 'title'),
                'content' => data_get($article, 'description', ''),
                'date' => data_get($article, 'publishedAt'),
                'category' => '',
                'source' => 'news-api',
                'author' => explode(',', data_get($article, 'author'))[0] ?: '',
            ]);
            $bar->advance();
        });

        $bar->finish();
        $this->newLine()->info("Created $count articles");

        return self::SUCCESS;
    }
}
