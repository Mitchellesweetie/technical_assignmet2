<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stock Performance Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>

<body>
    <header class="topbar">
        <div class="brand">
            <h1>Stock Performance Dashboard</h1>
            <p>Upload prices and discover the top 5 gainers for the period.</p>
        </div>
        <div class="topbar-actions">
            <span class="user-pill">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">Log out</button>
            </form>
        </div>
    </header>

    <main class="container">
        <div class="grid">
            <section class="card">
                <h2>Upload stock prices</h2>
                <p>Supported formats: CSV, XLS, XLSX, and ODS. The file should include stock name, price, and date columns.</p>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('stocks.upload') }}" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    <div class="upload-box">
                        <label class="upload-label" for="stock_file">
                            Choose file
                        </label>
                        <input id="stock_file" type="file" name="stock_file" accept=".csv,.txt,.xls,.xlsx,.ods" required>
                        <div class="file-name" id="fileName">No file selected</div>
                    </div>
                    <button type="submit" class="submit-btn" id="uploadBtn">Upload and analyze</button>
                </form>

                @if ($upload)
                    <div class="meta">
                        <div><strong>Latest file:</strong> {{ $upload->filename }}</div>
                        <div><strong>Uploaded:</strong> {{ $upload->created_at->format('M j, Y g:i A') }}</div>
                        @if ($analysis)
                            <div><strong>Period:</strong> {{ $analysis['period']['start'] }} to {{ $analysis['period']['end'] }}</div>
                        @endif
                    </div>
                @endif
            </section>

            <section class="card">
                <div class="chart-header">
                    <div>
                        <h2>Top 5 performers</h2>
                        <p>Histogram of the top 5 stocks ranked by highest price gain during the selected period.</p>
                    </div>
                    {{-- @if ($analysis)
                        <span class="period-badge">
                            {{ $analysis['period']['start'] }} &rarr; {{ $analysis['period']['end'] }}
                        </span>
                    @endif --}}
                </div>

                @if ($analysis && $analysis['performers']->isNotEmpty())
                    <div class="chart-wrap">
                        <canvas id="performanceChart"></canvas>
                    </div>

                    {{-- <div class="performers">
                        @foreach ($analysis['performers'] as $index => $performer)
                            <article class="performer-card">
                                <div class="rank">#{{ $index + 1 }} performer</div>
                                <h3>{{ $performer['stock'] }}</h3>
                                <div class="gain">
                                    +{{ number_format($performer['gain'], 2) }}
                                    <small>+{{ number_format($performer['gain_percent'], 2) }}% from {{ number_format($performer['start_price'], 2) }}</small>
                                </div>
                            </article>
                        @endforeach
                    </div> --}}
                @else
                    <div class="empty-state">
                        <div>
                            <h3>No data to display yet</h3>
                            <p>Upload a stock price file to see the top 5 performers chart.</p>
                        </div>
                    </div>
                @endif
            </section>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const fileInput = document.getElementById('stock_file');
        const fileName = document.getElementById('fileName');
        const uploadForm = document.getElementById('uploadForm');
        const uploadBtn = document.getElementById('uploadBtn');

        fileInput.addEventListener('change', () => {
            fileName.textContent = fileInput.files[0]?.name || 'No file selected';
        });

        uploadForm.addEventListener('submit', () => {
            uploadBtn.disabled = true;
            uploadBtn.textContent = 'Uploading...';
        });

        @if ($analysis && $analysis['performers']->isNotEmpty())
            const chartData = @json($analysis['chart']);

            new Chart(document.getElementById('performanceChart'), {
                type: 'bar',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            callbacks: {
                                title(context) {
                                    return context[0]?.label ?? '';
                                },
                                label(context) {
                                    const value = context.parsed.y;
                                    return `Gain: +${value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                                },
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 0,
                                autoSkip: false,
                                font: {
                                    size: 11,
                                },
                            },
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Price gain',
                                font: {
                                    weight: '600',
                                },
                            },
                            grid: {
                                color: '#e2e8f0',
                            },
                            ticks: {
                                callback(value) {
                                    return Number(value).toLocaleString();
                                },
                            },
                        },
                    },
                },
            });
        @endif
    </script>
</body>

</html>
