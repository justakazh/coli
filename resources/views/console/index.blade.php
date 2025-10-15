@extends('templates.v1')
@section('content')
@section('title', 'Console')
@push('styles')
<style>
    .xterm-container {
        width: 100%;
        height: 100%;
        padding: 20px;
        background: #17191A;
        border-radius: 8px;
        margin-top: 20px;
        overflow: hidden;
    }
</style>
@endpush
<div class="container-fluid" >
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

    <div class="card" >
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0"> <i class="fas fa-terminal me-2"></i> Console</h3>
                @if($data['utils']->state == 'inactive')
                <form action="{{ route('console.action') }}" method="POST" class="mb-0">
                    @csrf
                    <input type="hidden" name="action" value="start">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-toggle-on me-2"></i> Start Console
                    </button>
                </form>
                @else
                <form action="{{ route('console.action') }}" method="POST" class="mb-0">
                    @csrf
                    <input type="hidden" name="action" value="stop">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-toggle-off me-2"></i> Stop Console
                    </button>
                </form>
                @endif
            </div>
            @if($data['utils']->state == 'inactive')
                <div class="alert alert-danger mt-2">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Console is inactive. Please start console to use it.
                </div>
            @else
                <div class="xterm-container" id="xterm-container"></div>
            @endif
        </div>
    </div>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const term = new Terminal({
        fontFamily: 'Fira Mono, Menlo, Consolas, monospace',
        fontSize: 14,
        lineHeight: 1.2,
        scrollback: 2000,
        cursorBlink: true,
        cursorStyle: 'bar',
        theme: {
            background: '#17191a',
            foreground: '#d8dee9',
            cursor: '#81a1c1',
            selection: '#434C5E77',
            black: '#151515',
            red: '#BF616A',
            green: '#A3BE8C',
            yellow: '#EBCB8B',
            blue: '#81A1C1',
            magenta: '#B48EAD',
            cyan: '#88C0D0',
            white: '#d8dee9',
            brightBlack: '#4c566a',
            brightRed: '#BF616A',
            brightGreen: '#A3BE8C',
            brightYellow: '#EBCB8B',
            brightBlue: '#81A1C1',
            brightMagenta: '#B48EAD',
            brightCyan: '#8FBCBB',
            brightWhite: '#ECEFF4',
        }
    });
    const fitAddon = new FitAddon.FitAddon();
    term.loadAddon(fitAddon);
    term.open(document.getElementById('xterm-container'));
    fitAddon.fit();

    // Resize handler to always fit terminal
    function fitTerminal() {
        fitAddon.fit();
        // Kirim ukuran terminal ke server jika socket telah tersambung
        if (socket && socket.connected) {
            socket.emit('resize', {cols: term.cols, rows: term.rows});
        }
    }
    window.addEventListener('resize', fitTerminal);

    // Util: WebSocket dynamic URL
    function getSocketUrl() {
        const proto = location.protocol === 'https:' ? 'wss:' : 'ws:';
        return `${proto}//${location.hostname}:5000`;
    }

    // Print Header (command tips) - warna jangan diubah
    function printHeader() {
        term.writeln('\x1b[1;34m╔════════════════════════════════════════════════╗\x1b[0m');
        term.writeln('\x1b[1;34m║        \x1b[1;37mWeb Console Terminal (Xterm.js)\x1b[1;34m        ║\x1b[0m');
        term.writeln('\x1b[1;34m╚════════════════════════════════════════════════╝\x1b[0m');
        term.writeln('');
        term.writeln(`\x1b[1;37mTips:\x1b[0m Use your keyboard as in a real terminal. Press Ctrl+C to interrupt processes.`);
        if (location.protocol === 'https:') {
            term.writeln('\x1b[32m[SECURE]\x1b[0m Your connection is encrypted.');
        } else {
            term.writeln('\x1b[31m[INSECURE]\x1b[0m For best security, connect via HTTPS.');
        }
        term.writeln("");
        term.writeln('\x1b[1;33mCopy & Paste usage:\x1b[0m');
        term.writeln('- To \x1b[1mcopy\x1b[0m: Select your desired text and press \x1b[1mCtrl+Shift+X\x1b[0m.');
        term.writeln('- To \x1b[1mpaste\x1b[0m: Place cursor and press \x1b[1mCtrl+Shift+V\x1b[0m.');
        term.writeln('');
    }
    printHeader();

    // ===== FULL TTY via SOCKET.IO =====
    const socket = io(getSocketUrl(), {
        transports: ['websocket'],
        reconnection: true,
        reconnectionAttempts: 5,
        secure: location.protocol === 'https:'
    });

    // Send initial size when connected
    function sendResize() {
        if (socket && socket.connected) {
            socket.emit('resize', { cols: term.cols, rows: term.rows });
        }
    }

    socket.on('connect', function () {
        term.write('\r\n\x1b[32m✔ Connected\x1b[0m to terminal host [' +
            (location.protocol === 'https:' ? 'wss' : 'ws') + ' websocket]\r\n');
        socket.emit('start_terminal', {cols: term.cols, rows: term.rows});
        sendResize();
        term.focus();
    });

    socket.on('disconnect', function () {
        term.writeln('\r\n\x1b[31m✖ Disconnected from terminal server\x1b[0m');
    });

    // Kirim ukuran terminal ketika ukurannya berubah
    term.onResize(e => {
        // e.cols, e.rows
        if (socket && socket.connected) {
            socket.emit('resize', { cols: e.cols, rows: e.rows });
        }
    });

    // Tampilkan OUTPUT TTY penuh
    socket.on('output', function (data) {
        if (typeof data.output !== 'string' || data.output.length === 0) return;
        term.write(data.output);
        term.scrollToBottom();
    });

    // Kirim input keyboard langsung ke TTY server
    term.onData(data => {
        socket.emit('input', { input: data });
    });

    // Handler untuk copy & paste
    term.attachCustomKeyEventHandler((ev) => {
        // Ctrl+Shift+X => Copy Selection
        if ((ev.ctrlKey || ev.metaKey) && ev.shiftKey && ev.key.toLowerCase() === 'x') {
            let selected = window.getSelection().toString();
            if (!selected) {
                selected = term.getSelection();
            }
            if (selected) {
                // Create a temporary textarea element to copy programmatically
                const textarea = document.createElement('textarea');
                textarea.value = selected;
                document.body.appendChild(textarea);
                textarea.select();
                try {
                    document.execCommand('copy');
                } catch (err) {}
                document.body.removeChild(textarea);
            }
            return false;
        }
        // Ctrl+Shift+V => Paste from clipboard
        if ((ev.ctrlKey || ev.metaKey) && ev.shiftKey && ev.key.toLowerCase() === 'v') {
            // Try using the Async Clipboard API if available
            if (navigator.clipboard && navigator.clipboard.readText) {
                navigator.clipboard.readText().then(function(text) {
                    if (text) {
                        socket.emit('input', { input: text });
                    }
                });
            } else if (term.textarea) {
                term.textarea.focus();
                setTimeout(() => {}, 50);
            }
            return false;
        }
        // Block browser reload events
        if ((ev.ctrlKey || ev.metaKey) && ev.key === 'r') return false;
        if (ev.key === 'F5') return false;
        return true;
    });

    // Enable native paste via regular Ctrl+V, right-click, etc
    if (term.textarea) {
        term.textarea.addEventListener('paste', function(e) {
            const pasted = e.clipboardData.getData('text');
            if (pasted) {
                socket.emit('input', { input: pasted });
            }
            e.preventDefault();
        });
    }

    // Allow right-click menu for copy/paste
    if (term.element) {
        term.element.addEventListener('contextmenu', function(e) {
            // Do not prevent default; user can use browser menu
        });
    }

    term.focus();
});
</script>
@endpush

@endsection
