@extends('app.template')
@section('title', 'Review Result - COLI')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    <i class="mdi mdi-radar me-2"></i>
                    Review Result
                </h5>

                @if(count($header) > 0 && $paginatedRows->count() > 0)
                    <div class="table-responsive">
                        <table id="result-table" class="table table-bordered table-hover align-middle">
                            <thead class="">
                                <tr>
                                    @foreach($header as $col)
                                        <th>{{ $col }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paginatedRows as $row)
                                    <tr>
                                        @foreach($row as $cell)
                                            <td>{{ $cell }}</td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($header) }}" class="text-center">No data to display.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div>
                    <div class="d-flex justify-content-center mt-4">
                    {{ $paginatedRows->withQueryString()->links('pagination::bootstrap-4') }}
                </div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        No parsing result to display.
                    </div>
                @endif

                <a href="{{ route('scans.index') }}" class="btn btn-secondary mt-3">
                    <i class="mdi mdi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all table cells in the result table
    var table = document.getElementById('result-table');
    if (table) {
        var tds = table.querySelectorAll('tbody td');
        tds.forEach(function(td) {
            var text = td.textContent.trim();
            if (text.startsWith('http://') || text.startsWith('https://')) {
                // Create a link element
                var a = document.createElement('a');
                a.href = text;
                a.target = '_blank';
                a.rel = 'noopener noreferrer';
                a.textContent = text;
                // Replace the cell content with the link
                td.textContent = '';
                td.appendChild(a);
            }
        });
    }
});
</script>

@endsection
