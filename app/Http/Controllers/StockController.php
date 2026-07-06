<?php

namespace App\Http\Controllers;

use App\Models\StockPrice;
use App\Models\StockUpload;
use App\Services\StockFileParser;
use App\Services\TopPerformersAnalyzer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function __construct(
        private readonly StockFileParser $parser,
        private readonly TopPerformersAnalyzer $analyzer,
        
    ) {}

    public function dashboard()
    {
        $upload = StockUpload::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->first();

        $analysis = $upload ? $this->analyzer->analyze($upload) : null;

        return view('dashboard', [
            'upload' => $upload,
            'analysis' => $analysis,
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'stock_file' => ['required', 'file', 'mimes:csv,txt,xls,xlsx,ods', 'max:10240'],
        ]);

        $file = $request->file('stock_file');
        $records = $this->parser->parse($file);

        DB::transaction(function () use ($file, $records) {
            $upload = StockUpload::query()->create([
                'user_id' => auth()->id(),
                'filename' => $file->getClientOriginalName(),
            ]);

            $payload = collect($records)->map(fn (array $record) => [
                'stock_upload_id' => $upload->id,
                'stock_name' => $record['stock'],
                'price' => $record['price'],
                'date' => $record['date'],
                'created_at' => now(),
                'updated_at' => now(),
            ])->all();

            foreach (array_chunk($payload, 500) as $chunk) {
                StockPrice::query()->insert($chunk);
            }
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'Stock prices uploaded successfully. Showing top performers.');
    }
}