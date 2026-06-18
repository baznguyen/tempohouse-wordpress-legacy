#!/usr/bin/env node
/**
 * TEMPO House — FTP/SSH Deploy Script
 *
 * Uploads the Next.js static export (./out) to the production server.
 * Supports both FTP and SFTP (SSH) modes.
 *
 * Usage:
 *   npm run deploy          — full build + deploy (coming-soon, main domain)
 *   npm run deploy:dev      — preview build + deploy (full site, dev subdomain)
 *   npm run deploy:dry      — dry run, prints what would be uploaded
 *
 * Required env vars (add to .env.local):
 *   DEPLOY_MODE=ftp|sftp
 *   FTP_HOST / SSH_HOST, FTP_USER / SSH_USER, FTP_PASSWORD / SSH_KEY_PATH
 *   REMOTE_PATH          — server path for main domain  (e.g. /public_html)
 *   REMOTE_PATH_DEV      — server path for dev subdomain (e.g. /public_html/dev)
 */

require("dotenv").config({ path: ".env.local" });

const fs = require("fs");
const path = require("path");

// DEPLOY_ENV=dev targets the dev subdomain remote path
const DEPLOY_ENV = process.env.DEPLOY_ENV ?? "production";

const {
  DEPLOY_MODE = "ftp",   // "ftp" | "sftp"
  FTP_HOST,
  FTP_PORT = "21",
  FTP_USER,
  FTP_PASSWORD,
  SSH_HOST,
  SSH_PORT = "22",
  SSH_USER,
  SSH_KEY_PATH,
  SSH_PASSWORD,
  REMOTE_PATH      = "/public_html",
  REMOTE_PATH_DEV  = "/public_html/dev",
  DRY_RUN,
} = process.env;

const EFFECTIVE_REMOTE = DEPLOY_ENV === "dev" ? REMOTE_PATH_DEV : REMOTE_PATH;

const OUT_DIR = path.resolve(__dirname, "../out");
const isDryRun = DRY_RUN === "true";

if (!fs.existsSync(OUT_DIR)) {
  console.error("❌  ./out directory not found. Run `npm run build` first.");
  process.exit(1);
}

function walkDir(dir, base = dir) {
  const entries = fs.readdirSync(dir, { withFileTypes: true });
  return entries.flatMap((e) => {
    const full = path.join(dir, e.name);
    if (e.isDirectory()) return walkDir(full, base);
    return [{ local: full, remote: path.relative(base, full).replace(/\\/g, "/") }];
  });
}

async function deployFTP() {
  if (!FTP_HOST || !FTP_USER || !FTP_PASSWORD) {
    console.error("❌  Missing FTP env vars: FTP_HOST, FTP_USER, FTP_PASSWORD");
    process.exit(1);
  }

  const Ftp = require("node-ftp");
  const files = walkDir(OUT_DIR);

  console.log(`📦  FTP deploy → ${FTP_USER}@${FTP_HOST}:${EFFECTIVE_REMOTE}`);
  if (isDryRun) {
    console.log(`🧪  DRY RUN — ${files.length} files would be uploaded`);
    files.forEach((f) => console.log(`   ${EFFECTIVE_REMOTE}/${f.remote}`));
    return;
  }

  const client = new Ftp();
  await new Promise((resolve, reject) => {
    client.on("ready", async () => {
      try {
        for (const { local, remote } of files) {
          const remotePath = `${EFFECTIVE_REMOTE}/${remote}`;
          const remoteDir = remotePath.substring(0, remotePath.lastIndexOf("/"));
          await new Promise((res, rej) =>
            client.mkdir(remoteDir, true, (e) => (e ? rej(e) : res()))
          );
          await new Promise((res, rej) =>
            client.put(local, remotePath, (e) => (e ? rej(e) : res()))
          );
          console.log(`  ✓ ${remote}`);
        }
        client.end();
        resolve();
      } catch (err) {
        client.end();
        reject(err);
      }
    });
    client.on("error", reject);
    client.connect({ host: FTP_HOST, port: Number(FTP_PORT), user: FTP_USER, password: FTP_PASSWORD });
  });

  console.log(`✅  FTP deploy complete — ${files.length} files uploaded`);
}

async function deploySFTP() {
  if (!SSH_HOST || !SSH_USER) {
    console.error("❌  Missing SFTP env vars: SSH_HOST, SSH_USER");
    process.exit(1);
  }
  if (!SSH_KEY_PATH && !SSH_PASSWORD) {
    console.error("❌  Provide SSH_KEY_PATH or SSH_PASSWORD");
    process.exit(1);
  }

  const { Client } = require("ssh2");
  const files = walkDir(OUT_DIR);

  console.log(`📦  SFTP deploy → ${SSH_USER}@${SSH_HOST}:${EFFECTIVE_REMOTE}`);
  if (isDryRun) {
    console.log(`🧪  DRY RUN — ${files.length} files would be uploaded`);
    files.forEach((f) => console.log(`   ${EFFECTIVE_REMOTE}/${f.remote}`));
    return;
  }

  const conn = new Client();
  await new Promise((resolve, reject) => {
    conn.on("ready", () => {
      conn.sftp(async (err, sftp) => {
        if (err) { conn.end(); return reject(err); }

        // Resolve home dir so absolute paths like /public_html resolve correctly
        // even when the SFTP session is not chrooted to /.
        const homeDir = await new Promise((res, rej) =>
          sftp.realpath(".", (e, p) => (e ? rej(e) : res(p)))
        );
        const resolvePath = (p) =>
          p.startsWith("/") ? `${homeDir}${p}` : `${homeDir}/${p}`;
        const resolvedRemote = resolvePath(EFFECTIVE_REMOTE);
        console.log(`  ℹ  home=${homeDir}  remote=${resolvedRemote}`);

        // stat-then-mkdir per path segment; skip segments that already exist.
        const mkdirp = async (dir) => {
          const segments = dir.replace(/^\//, "").split("/");
          let current = "";
          for (const seg of segments) {
            current += "/" + seg;
            const exists = await new Promise((res) =>
              sftp.stat(current, (e) => res(!e))
            );
            if (!exists) {
              await new Promise((res, rej) =>
                sftp.mkdir(current, (e) => (e ? rej(e) : res()))
              );
            }
          }
        };

        try {
          for (const { local, remote } of files) {
            const remotePath = `${resolvedRemote}/${remote}`;
            const remoteDir = remotePath.substring(0, remotePath.lastIndexOf("/"));
            await mkdirp(remoteDir);
            await new Promise((res, rej) =>
              sftp.fastPut(local, remotePath, (e) => (e ? rej(e) : res()))
            );
            console.log(`  ✓ ${remote}`);
          }
          conn.end();
          resolve();
        } catch (e) {
          conn.end();
          reject(e);
        }
      });
    });
    conn.on("error", reject);

    const connectConfig = {
      host: SSH_HOST,
      port: Number(SSH_PORT),
      username: SSH_USER,
      ...(SSH_KEY_PATH
        ? { privateKey: fs.readFileSync(SSH_KEY_PATH) }
        : { password: SSH_PASSWORD }),
    };
    conn.connect(connectConfig);
  });

  console.log(`✅  SFTP deploy complete — ${files.length} files uploaded`);
}

(async () => {
  try {
    if (DEPLOY_MODE === "sftp") {
      await deploySFTP();
    } else {
      await deployFTP();
    }
  } catch (err) {
    console.error("❌  Deploy failed:", err.message);
    process.exit(1);
  }
})();
