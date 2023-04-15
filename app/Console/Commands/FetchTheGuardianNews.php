<?php

namespace App\Console\Commands;

use App\Http\Integrations\News\Requests\TheGuardianRequest;
use App\Models\Article;
use Illuminate\Console\Command;

class FetchTheGuardianNews extends Command
{
    private const PER_PAGE_LIMIT = 10;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-the-guardian-news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news from The Guardian';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $request = new TheGuardianRequest;

        $request->query()
            ->add('page-size', self::PER_PAGE_LIMIT)
            ->add('from-date', now()->subDays(1)->format('Y-m-j'))
            ->add('show-fields', 'headline,body')
            ->add('show-tags', 'contributor');

        $response = $request->send();
        $decodedBody = $response->json();
        $responseBody = collect(data_get($decodedBody, 'response.results', []));

        $count = count($responseBody);
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $responseBody->each(function ($article) use ($bar) {
            $author = collect(data_get($article, 'tags'))->first(fn($tag) => $tag === 'contributor');
            $authorName = data_get($author, 'firstName') . ' ' . data_get($author, 'lastName');

            Article::create([
                'title' => data_get($article, 'fields.headline', data_get($article, 'webTitle')),
                'content' => data_get($article, 'fields.body'),
                'date' => data_get($article, 'webPublicationDate'),
                'category' => data_get($article, 'type'),
                'source' => 'the-guardian',
                'author' => $authorName,
            ]);
            $bar->advance();
        });

        $bar->finish();
        $this->newLine()->info("Created $count articles");

        return self::SUCCESS;
    }
}
