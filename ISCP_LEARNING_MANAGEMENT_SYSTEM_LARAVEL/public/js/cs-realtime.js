//  FILE : public/js/cs-realtime.js

import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
import {
    getDatabase,
    ref,
    push,
    onChildAdded,
    serverTimestamp,
} from "https://www.gstatic.com/firebasejs/10.12.2/firebase-database.js";

//  INIT FIREBASE
const app = initializeApp(window.FirebaseConfig);
const db = getDatabase(app);

//  DOM ELEMENTS
const chatBox = document.getElementById("chatBox");
const roomId = document.getElementById("roomId").value;
const sender = document.getElementById("sender").value;
const msgInput = document.getElementById("msgInput");
const sendForm = document.getElementById("sendForm");

//  BUBBLE STYLE MAP
const bubbleTypeMap = {
    admin: "bubble-admin",
    pelapor: "bubble-user",
    sistem: "bubble-system",
    system: "bubble-system",
};

//  FUNGSI: Tampilkan Bubble Pesan
const currentUser = document.getElementById("currentUser").value;

function addMessageBubble(data) {
    let bubbleClass = "";

    // Jika pesan dari saya → hijau kanan
    if (data.sender === currentUser) {
        bubbleClass = "bubble-me";
    } else {
        bubbleClass = "bubble-other";
    }

    // Pesan sistem tetap khusus
    if (
        data.sender.toLowerCase() === "sistem" ||
        data.sender.toLowerCase() === "system"
    ) {
        bubbleClass = "bubble-system";
    }

    const wrapper = document.createElement("div");
    wrapper.classList.add(bubbleClass);

    wrapper.innerHTML = `
        ${
            data.sender !== "Sistem"
                ? `<div class="sender-name">${data.sender}</div>`
                : ""
        }
        ${data.message}
        ${
            bubbleClass !== "bubble-system"
                ? `<div class="bubble-time">${
                      data.full_time || data.time
                  }</div>`
                : ""
        }
    `;

    chatBox.appendChild(wrapper);
    chatBox.scrollTop = chatBox.scrollHeight;
}

//  LISTENER: Saat ada pesan masuk
import {
    query,
    orderByChild,
} from "https://www.gstatic.com/firebasejs/10.12.2/firebase-database.js";

const msgRef = query(
    ref(db, `cs_rooms/${roomId}/messages`),
    orderByChild("ts")
);

onChildAdded(msgRef, (snap) => {
    const data = snap.val();
    if (!data) return;
    addMessageBubble(data);
});

//  KIRIM PESAN
if (sendForm) {
    sendForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const message = msgInput.value.trim();
        if (!message) return;

        await push(ref(db, `cs_rooms/${roomId}/messages`), {
            id: "msg_" + Date.now(),
            sender, // Admin(username#id) atau Pelapor
            message,

            // waktu singkat (HH:MM)
            time: new Date().toLocaleTimeString([], {
                hour: "2-digit",
                minute: "2-digit",
            }),

            // Waktu lengkap: YYYY-MM-DD HH:MM:SS
            full_time: new Date()
                .toLocaleString("sv-SE", {
                    year: "numeric",
                    month: "2-digit",
                    day: "2-digit",
                    hour: "2-digit",
                    minute: "2-digit",
                    second: "2-digit",
                })
                .replace("T", " "),

            ts: serverTimestamp(),
        });

        msgInput.value = "";
    });
}

// --- Render File ---
export function renderFile(file) {
    let isImage = ["jpg", "jpeg", "png", "gif", "webp"].includes(file.ext);

    return `
        <div class="col-4">
            <div class="file-item border rounded p-1 text-center">

                ${
                    isImage
                        ? `<div class="file-thumb">
                            <img src="${file.url}" class="thumb-img"
                                 onclick="previewImage('${file.url}')">
                       </div>`
                        : `<div class="file-thumb d-flex align-items-center justify-content-center">
                            <i class="bi bi-file-earmark-text" style="font-size:32px;"></i>
                       </div>`
                }

                <div class="file-name text-truncate mt-1" title="${file.name}">
                    ${file.name}
                </div>

            </div>
        </div>
    `;
}

// --- Load File List ---
export function loadFiles(roomId = null, endpoint = null, containerId = null) {
    if (!roomId || !endpoint || !containerId) return;

    let container = document.getElementById(containerId);

    fetch(`${endpoint}/${roomId}`)
        .then((res) => res.json())
        .then((res) => {
            container.innerHTML = "";

            if (!res.success || res.files.length === 0) {
                container.innerHTML = `<div class="text-center text-muted">Belum ada file</div>`;
                return;
            }

            res.files.forEach((f) => {
                container.innerHTML += renderFile(f);
            });
        });
}

// --- Realtime Polling (opsional) ---
export function enableRealtime(roomId, endpoint, targetId) {
    setInterval(() => {
        loadFiles(roomId, endpoint, targetId);
    }, 2000);
}

// --- Global Preview Function ---
window.previewImage = function (src) {
    document.getElementById("modalPreviewImg").src = src;

    let modal = new bootstrap.Modal(
        document.getElementById("imagePreviewModal")
    );
    modal.show();
};
