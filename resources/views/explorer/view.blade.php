@extends('templates.v1')
@section('content')
@section('title', 'Explorer')

<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show my-2" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show my-2" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Breadcrumb --}}
    @php
        $scanId = $data['scan']->id;
        $baseRoute = route('explorer', $scanId);
        $path = request('path', '');
        $segments = $path ? explode('/', $path) : [];
        $constructedPath = '';
        $searchKeyword = request('search', '');
        $fileContent = $data['content'];
    @endphp

    <div class="card shadow mb-3">
        <div class="card-body">
            <nav aria-label="breadcrumb" class="mb-3 d-flex justify-content-center">
                <ol class="breadcrumb p-2 rounded shadow mb-0 align-items-center mx-auto">
                    <li class="breadcrumb-item">
                        <a href="{{ $baseRoute }}" class="text-decoration-none d-flex align-items-center">
                            <i class="fa fa-hdd me-1 text-warning"></i> <span class="fw-bold text-warning">Root</span>
                        </a>
                    </li>
                    @foreach($segments as $i => $segment)
                        @php
                            $constructedPath .= ($i === 0 ? '' : '/') . $segment;
                            $link = $baseRoute . '?path=' . urlencode($constructedPath);
                        @endphp
                        @if ($i < count($segments) - 1)
                            <li class="breadcrumb-item">
                                <a href="{{ $link }}" class="text-decoration-none text-warning">{{ $segment }}</a>
                            </li>
                        @else
                            <li class="breadcrumb-item active fw-semibold text-warning" aria-current="page">{{ $segment }}</li>
                        @endif
                    @endforeach
                </ol>
            </nav>
            <div class="row">
                <div class="col-md-3 mb-4 mb-lg-0">
                    <div class="card border-secondary h-100">
                        <div class="card-body">
                            
                            <table class="table table-bordered table-sm small mb-3" width="100%">
                                <tbody>
                                    <tr>
                                        <th class="text-nowrap">Name</th>
                                        <td>
                                            {{ $data['info']['name'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-nowrap">Type</th>
                                        <td>
                                            <span class="badge bg-secondary text-capitalize px-2 py-1">
                                                {{ $data['info']['type'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-nowrap">Size</th>
                                        <td>
                                            {{ number_format($data['info']['size']) }} bytes
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-nowrap">Modified</th>
                                        <td>
                                            {{ \Carbon\Carbon::createFromTimestamp($data['info']['modified'])->format('Y-m-d H:i') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-nowrap">Permissions</th>
                                        <td>
                                            <span class="font-monospace">{{ $data['info']['permissions'] }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Search inside pre area -->
                            <div class="mb-3">
                                <label for="search-in-pre" class="form-label fw-bold mb-1">Search in File Preview</label>
                                <div class="input-group">
                                    <input type="text" id="search-in-pre" class="form-control" placeholder="Find text in preview..." onkeyup="highlightInPre()" autocomplete="off" />
                                    <button class="btn btn-secondary btn-sm" type="button" id="btn-prev" onclick="searchPrev()" title="Previous"><i class="fas fa-arrow-up"></i></button>
                                    <button class="btn btn-secondary btn-sm" type="button" id="btn-next" onclick="searchNext()" title="Next"><i class="fas fa-arrow-down"></i></button>
                                </div>
                                <div id="search-match-info" class="small text-muted mt-1"></div>
                            </div>

                            
                        </div>
                    </div>
                </div>
                <div class="col-md-9 col-md-7">
                    <div class="card border-light h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <label class="form-label fw-semibold mb-0">
                                    File Preview
                                </label>
                                <a href="{{ route('explorer.download', $data['scan']->id) }}?path={{ urlencode(request('path')) }}" class="btn btn-primary btn-sm" >
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                            <pre id="file-preview" class="p-3 rounded border small flex-grow-1 mb-0" style=" overflow:auto;">{{ $fileContent }}</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function escapeHtml(text) {
    if (!text) return '';
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");
}

// match navigation state
let matchIndices = [];
let currentMatchIndex = 0;
let latestSearch = "";
let rawText = {!! json_encode($fileContent) !!}; // NO ` character wrapping

function highlightText(text, search, markIndex = null) {
    if (!search) return escapeHtml(text);

    let regex;
    try {
        regex = new RegExp("(" + search.replace(/[.*+\-?^${}()|[\]\\]/g, '\\$&') + ")", "gi");
    } catch (e) {
        return escapeHtml(text);
    }

    let i = 0;
    // Highlight all matches, but apply a custom class to the active one (for navigation)
    let html = escapeHtml(text).replace(regex, function(_, g1) {
        let c = (markIndex !== null && i === markIndex) ? 'current-match' : '';
        let res = '<mark class="'+c+'">' + g1 + '</mark>';
        i++;
        return res;
    });

    // update matches
    matchIndices = [];
    let m;
    // Use a global regex to find the indexes
    let r = new RegExp(search.replace(/[.*+\-?^${}()|[\]\\]/g, '\\$&'), "gi");
    while ((m = r.exec(text)) !== null) {
        matchIndices.push(m.index);
        // If infinite loop, force break as safeguard
        if (m.index === r.lastIndex) r.lastIndex++;
    }

    if (markIndex !== null) {
        currentMatchIndex = markIndex;
    } else {
        currentMatchIndex = matchIndices.length > 0 ? 0 : -1;
    }

    updateMatchInfo();
    return html;
}

function updateMatchInfo() {
    var info = document.getElementById('search-match-info');
    if (!latestSearch || matchIndices.length === 0) {
        info.textContent = "No matches";
    } else {
        info.textContent = "Result " + (currentMatchIndex + 1) + " of " + matchIndices.length;
    }
    toggleNavButtons();
}

function highlightInPre() {
    var search = document.getElementById('search-in-pre').value;
    var pre = document.getElementById('file-preview');

    latestSearch = search;
    currentMatchIndex = 0;
    pre.innerHTML = highlightText(rawText, search, matchIndices.length > 0 ? 0 : null);

    // Scroll to the currently highlighted match
    setTimeout(() => {
        scrollToCurrentMatch();
    }, 0);
}

function searchNext() {
    if (matchIndices.length === 0) return;

    currentMatchIndex++;
    if (currentMatchIndex >= matchIndices.length) {
        currentMatchIndex = 0;
    }
    rehighlightWithCurrent();
}

function searchPrev() {
    if (matchIndices.length === 0) return;

    currentMatchIndex--;
    if (currentMatchIndex < 0) {
        currentMatchIndex = matchIndices.length - 1;
    }
    rehighlightWithCurrent();
}

function rehighlightWithCurrent() {
    var pre = document.getElementById('file-preview');
    var search = latestSearch;
    pre.innerHTML = highlightText(rawText, search, currentMatchIndex);

    setTimeout(() => {
        scrollToCurrentMatch();
    }, 0);
}

function toggleNavButtons() {
    var prev = document.getElementById('btn-prev');
    var next = document.getElementById('btn-next');
    if (!matchIndices.length) {
        prev.disabled = true;
        next.disabled = true;
    } else {
        prev.disabled = false;
        next.disabled = false;
    }
}

function scrollToCurrentMatch() {
    var pre = document.getElementById('file-preview');
    var mark = pre.querySelector('mark.current-match');
    if (mark) {
        // Scroll the pre area so that the mark is visible
        mark.scrollIntoView({ behavior: "smooth", block: "center" });
    }
}

// When the page loads, initialize the highlighting with current search (if any)
document.addEventListener('DOMContentLoaded', function () {
    highlightInPre();
    document.getElementById('search-in-pre').addEventListener('input', highlightInPre);
});
</script>
@endpush

@push('styles')
<style>
mark.current-match {
    background: orange;
    color: black;
}
</style>
@endpush

@endsection
