<?php

namespace App\Console\Commands;

use App\Repositories\Line_notifyRepository;
use App\Service\LineNotifyService;
use Illuminate\Console\Command;

class Line extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'line:send {line_notify_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    /**
     * @var Line_notifyRepository
     */
    private $line_notifyRepository;
    /**
     * @var LineNotifyService
     */
    private $lineNotifyService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Line_notifyRepository $line_notifyRepository, LineNotifyService $lineNotifyService)
    {
        parent::__construct();

        $this->line_notifyRepository = $line_notifyRepository;
        $this->lineNotifyService = $lineNotifyService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $line_notify_id = $this->argument('line_notify_id');
        if ($line_notify_id == null) {
            $headers = ['Id	', 'Name', 'Description', 'Method'];
            $line_notifies = $this->line_notifyRepository->all()->only(['id', 'name', 'description', 'method'])->toArray();
            $this->table($headers, $line_notifies);
            $line_notify_id = $this->ask('Please input line notify id');
        }
        $result = $this->lineNotifyService->send($line_notify_id);
        if ($result[0] == true)
            $this->info($result[1]);
        else
            $this->error($result[1]);
    }
}
