import os
import pty
import select
import threading
import fcntl
import struct
import termios

from flask import Flask, request
from flask_socketio import SocketIO, emit

app = Flask(__name__)
socketio = SocketIO(app, cors_allowed_origins="*")

clients = {}

def set_pty_window_size(fd, rows, cols):
    """Set terminal window size for a pty fd."""
    if fd is not None:
        winsize = struct.pack("HHHH", rows, cols, 0, 0)
        try:
            fcntl.ioctl(fd, termios.TIOCSWINSZ, winsize)
        except Exception:
            pass

def read_pty_output(fd, sid):
    """Loop baca output dari shell dan kirim ke client."""
    while True:
        socketio.sleep(0.01)
        if sid not in clients:
            break
        try:
            r, _, _ = select.select([fd], [], [], 0)
            if fd in r:
                output = os.read(fd, 4096).decode(errors="ignore")
                if not output:
                    break
                socketio.emit("output", {"output": output}, to=sid)
        except OSError:
            break

@socketio.on("connect")
def on_connect():
    print(f"[+] Client connected: {request.sid}")

@socketio.on("disconnect")
def on_disconnect():
    sid = request.sid
    if sid in clients:
        try:
            os.killpg(os.getpgid(clients[sid]["pid"]), 9)
        except Exception:
            pass
        del clients[sid]
        print(f"[-] Client {sid} disconnected")

@socketio.on("start_terminal")
def on_start_terminal(data=None):
    sid = request.sid
    if sid in clients:
        emit("output", {"output": "[!] Terminal already running.\n"})
        return

    # Only the child of fork() should call setsid(), but it can fail due to OS/container permissions.
    # We'll skip setsid() due to PermissionError.
    pid, fd = pty.fork()
    if pid == 0:
        # CHILD - Full TTY (pseudo terminal) session
        # Skipping os.setsid() due to container/restricted envs.
        shell = os.environ.get("SHELL", "/bin/bash")
        os.environ["TERM"] = "xterm-256color"
        if data and "cols" in data and "rows" in data:
            set_pty_window_size(0, data["rows"], data["cols"])
        os.execvp(shell, [shell])
    else:
        # PARENT - store process info for this client
        clients[sid] = {"pid": pid, "fd": fd}
        if data and "cols" in data and "rows" in data:
            set_pty_window_size(fd, data["rows"], data["cols"])
        threading.Thread(target=read_pty_output, args=(fd, sid), daemon=True).start()
        emit("output", {"output": f"[*] Shell started (PID {pid})\n"})

@socketio.on("input")
def on_input(data):
    sid = request.sid
    if sid not in clients:
        emit("output", {"output": "[!] No active shell.\n"})
        return
    try:
        os.write(clients[sid]["fd"], data["input"].encode())
    except OSError:
        emit("output", {"output": "[!] Shell closed.\n"})

@socketio.on("resize")
def on_resize(data):
    # data: {rows: n, cols: n}
    sid = request.sid
    if sid not in clients:
        return
    fd = clients[sid].get("fd")
    if fd is not None and "rows" in data and "cols" in data:
        set_pty_window_size(fd, data["rows"], data["cols"])

if __name__ == "__main__":
    print("[*] Starting full TTY terminal WebSocket server on port 5000...")
    socketio.run(app, host="0.0.0.0", port=5000, allow_unsafe_werkzeug=True)
