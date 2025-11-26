//  FILE : public/js/complaint-realtime.js

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
function addMessageBubble(data) {
    let bubbleClass = "bubble-user";

    // Map sender → class bubble
    if (data.sender.startsWith("Admin(")) {
        bubbleClass = "bubble-admin";
    } else if (data.sender.toLowerCase() === "pelapor") {
        bubbleClass = "bubble-user";
    } else if (
        data.sender.toLowerCase() === "sistem" ||
        data.sender.toLowerCase() === "system"
    ) {
        bubbleClass = "bubble-system";
    }

    const wrapper = document.createElement("div");
    wrapper.classList.add(bubbleClass);

    // Nama pengirim (tapi tidak untuk Sistem)
    let senderLabel = "";
    if (!data.sender.toLowerCase().includes("sistem")) {
        senderLabel = `<div class="sender-name">${data.sender}</div>`;
    }

    // Jika bukan sistem → tampilkan waktu
    const timeLabel = !data.sender.toLowerCase().includes("sistem")
        ? `<div class="bubble-time">${data.full_time || data.time}</div>`
        : "";

    wrapper.innerHTML = `
        ${senderLabel}
        ${data.message}
        ${timeLabel}
    `;

    chatBox.appendChild(wrapper);
    chatBox.scrollTop = chatBox.scrollHeight;
}

//  LISTENER: Saat ada pesan masuk
onChildAdded(ref(db, `complaint_rooms/${roomId}/messages`), (snap) => {
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

        await push(ref(db, `complaint_rooms/${roomId}/messages`), {
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
