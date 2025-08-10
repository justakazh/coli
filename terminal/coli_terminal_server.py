#!/usr/bin/env python3
import argparse
from flask import Flask
from flask_socketio import SocketIO, disconnect
import pty
import os
import subprocess
import select
import termios
import struct
import fcntl
import shlex
import logging
import sys

logging.getLogger("werkzeug").setLevel(logging.ERROR)

__version__ = "0.5.0.2"

app = Flask(__name__, template_folder=".", static_folder=".", static_url_path="")
app.config["SECRET_KEY"] = "YouNeedToChangeThis"
app.config["fd"] = None
app.config["child_pid"] = None
app.config["cmd"] = ["/bin/bash"]  # default fallback
socketio = SocketIO(app, cors_allowed_origins="*")

def set_winsize(fd, row, col, xpix=0, ypix=0):
    winsize = struct.pack("HHHH", row, col, xpix, ypix)
    fcntl.ioctl(fd, termios.TIOCSWINSZ, winsize)

def read_and_forward_pty_output():
    max_read_bytes = 1024 * 20
    while True:
        socketio.sleep(0.01)
        if app.config["fd"]:
            (data_ready, _, _) = select.select([app.config["fd"]], [], [], 0)
            if data_ready:
                output = os.read(app.config["fd"], max_read_bytes).decode(errors="ignore")
                socketio.emit("pty-output", {"output": output}, namespace="/pty")

@app.route("/")
def index():
    return "WebSocket Terminal Active"

@socketio.on("pty-input", namespace="/pty")
def pty_input(data):
    if app.config["fd"]:
        os.write(app.config["fd"], data["input"].encode())

@socketio.on("resize", namespace="/pty")
def resize(data):
    if app.config["fd"]:
        set_winsize(app.config["fd"], data["rows"], data["cols"])

@socketio.on("connect", namespace="/pty")
def connect(auth):
    token = auth.get("token", "") if auth else ""
    logging.info(f"Incoming connection with token: {token}")

    if not token or not validate_token(token):
        logging.warning("Token invalid or terminal is not started. Disconnecting.")
        disconnect()
        return

    logging.info("Token valid. Starting session.")

    if app.config["child_pid"]:
        return

    (child_pid, fd) = pty.fork()
    if child_pid == 0:
        env = os.environ.copy()
        try:
            os.chdir(os.path.expanduser("~"))
        except Exception as e:
            logging.error(f"Unable to change to home directory: {e}")
        if "TERM" not in env or not env["TERM"]:
            env["TERM"] = "xterm-256color"
        subprocess.run(app.config["cmd"], env=env)
    else:
        app.config["fd"] = fd
        app.config["child_pid"] = child_pid
        set_winsize(fd, 50, 50)
        socketio.start_background_task(target=read_and_forward_pty_output)
        logging.info("PTY child started successfully")

def validate_token(token):
    try:
        command = ["php", "artisan", "run:validate-ws-token", token]
        result = subprocess.run(
            command, cwd="../", capture_output=True, text=True
        )
        logging.debug(f"Token validation result: {result.returncode}, output: {result.stdout.strip()}")
        return result.returncode == 0
    except Exception as e:
        logging.error(f"Token validation error: {e}")
        return False

def main():
    parser = argparse.ArgumentParser(description="Secure Web Terminal")
    parser.add_argument("-p", "--port", default=5000, type=int)
    parser.add_argument("--host", default="127.0.0.1")
    parser.add_argument("--debug", action="store_true")
    parser.add_argument("--version", action="store_true")
    parser.add_argument("--command", default="bash")
    parser.add_argument("--cmd-args", default="")
    args = parser.parse_args()

    if args.version:
        print(__version__)
        exit(0)

    app.config["cmd"] = [args.command] + shlex.split(args.cmd_args)

    logging.basicConfig(
        format="\033[92mpyxtermjs >\033[0m %(levelname)s (%(funcName)s:%(lineno)d) %(message)s",
        stream=sys.stdout,
        level=logging.DEBUG if args.debug else logging.INFO,
    )

    socketio.run(app, debug=args.debug, port=args.port, host=args.host, allow_unsafe_werkzeug=True)

if __name__ == "__main__":
    main()
