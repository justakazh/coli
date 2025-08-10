<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>Terminal - COLI</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/xterm@4.11.0/css/xterm.css" />
    <style>
      body, html {
        height: 100%;
        min-height: 100vh;
        background: #23272e;
      }
      #terminal {
        background: #181c20;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.12);
        overflow: hidden;
        height: 100%;
        width: 100%;
      }
      .help-bar {
        background: #23272e;
        color: #bdbdbd;
        font-size: 0.95em;
        padding: 10px 32px;
        border-top: 1px solid #2c313a;
        display: flex;
        align-items: center;
        gap: 24px;
      }
      .help-bar span {
        background: #31363f;
        color: #e0e0e0;
        border-radius: 4px;
        padding: 2px 8px;
        margin-right: 8px;
        font-family: inherit;
        font-size: 0.95em;
      }
      @media (max-width: 600px) {
        .help-bar {
          padding: 10px 8px;
          font-size: 0.95em;
        }
      }
    </style>
  </head>
  <body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
      <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">Terminal</span>
        <span id="status" class="badge bg-danger ms-auto">Disconnected</span>
      </div>
    </nav>

    <main class="flex-fill d-flex flex-column" style="min-height:0;">
      <div class="flex-fill d-flex flex-column" style="min-height:0;">
        <div id="terminal" class="flex-fill"></div>
      </div>
    </main>


    <!-- Bootstrap JS (optional, for some interactive components) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/xterm@4.11.0/lib/xterm.js"></script>
    <script src="https://unpkg.com/xterm-addon-fit@0.5.0/lib/xterm-addon-fit.js"></script>
    <script src="https://unpkg.com/xterm-addon-web-links@0.4.0/lib/xterm-addon-web-links.js"></script>
    <script src="https://unpkg.com/xterm-addon-search@0.8.0/lib/xterm-addon-search.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.0.1/socket.io.min.js"></script>

    <script>
      const term = new Terminal({
        cursorBlink: true,
        macOptionIsMeta: true,
        scrollback: true,
        theme: {
          background: "#181c20",
          foreground: "#e0e0e0",
          cursor: "#7ecfff",
          selection: "#3a3f4b"
        }
      });
      term.attachCustomKeyEventHandler(customKeyEventHandler);
      const fit = new FitAddon.FitAddon();
      term.loadAddon(fit);
      term.loadAddon(new WebLinksAddon.WebLinksAddon());
      term.loadAddon(new SearchAddon.SearchAddon());

      term.open(document.getElementById("terminal"));
      fit.fit();
      term.resize(15, 50);
      fit.fit();
      // Afficher l'art ASCII correctement ligne par ligne
      const asciiArt = [
        "   ___ ___  _    ___ ",
        "  / __/ _ \\| |  |_ _|",
        " | (_| (_) | |__ | | ",
        "  \\___\\___/|____|___| Terminal",
        "                     ",
        "Tips: Use Ctrl+Shift+x to copy and Ctrl+Shift+v to paste.",
        "",
      ];
      asciiArt.forEach(line => term.writeln(line));


      term.onData((data) => {
        socket.emit("pty-input", { input: data });
      });

      const socket = io("/pty", {
          auth: {
              token: "{{ $token }}" // pastikan $token ini tidak kosong
          }
      });
      const status = document.getElementById("status");

      socket.on("pty-output", function (data) {
        term.write(data.output);
      });

      socket.on("connect", () => {
        fitToscreen();
        status.classList.remove("bg-danger");
        status.classList.add("bg-success");
        status.textContent = "Connected";
      });

      socket.on("disconnect", () => {
        status.classList.remove("bg-success");
        status.classList.add("bg-danger");
        status.textContent = "Disconnected";
      });

      function fitToscreen() {
        fit.fit();
        const dims = { cols: term.cols, rows: term.rows };
        socket.emit("resize", dims);
      }

      function debounce(func, wait_ms) {
        let timeout;
        return function (...args) {
          const context = this;
          clearTimeout(timeout);
          timeout = setTimeout(() => func.apply(context, args), wait_ms);
        };
      }

      /**
       * Handle copy and paste events
       */
      function customKeyEventHandler(e) {
        if (e.type !== "keydown") {
          return true;
        }
        if (e.ctrlKey && e.shiftKey) {
          const key = e.key.toLowerCase();
          if (key === "v") {
            // ctrl+shift+v: paste from clipboard
            navigator.clipboard.readText().then((toPaste) => {
              term.writeText(toPaste);
            });
            return false;
          } else if (key === "c" || key === "x") {
            // ctrl+shift+x: copy selection to clipboard
            const toCopy = term.getSelection();
            navigator.clipboard.writeText(toCopy);
            term.focus();
            return false;
          }
        }
        return true;
      }

      const wait_ms = 50;
      window.onresize = debounce(fitToscreen, wait_ms);
    </script>
  </body>
</html>