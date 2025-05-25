import formidable from "formidable";
import fs from "fs";
import path from "path";
import { initializeApp } from "firebase/app";
import { getDatabase, ref, push } from "firebase/database";

// ปิด bodyParser ของ Next.js
export const config = {
  api: {
    bodyParser: false,
  },
};

// Firebase config (ใส่ค่า config จริง)
const firebaseConfig = {
  apiKey: "AIzaSyBHgpqApo8nL3yL4aTkT0wutpb6sgRIs7M",
  authDomain: "permit-app-4969b.firebaseapp.com",
  databaseURL: "https://permit-app-4969b-default-rtdb.firebaseio.com",
  projectId: "permit-app-4969b",
  storageBucket: "permit-app-4969b.appspot.com",
  messagingSenderId: "721297118613",
  appId: "1:721297118613:web:7ff76a2f258db68e0b529c"
};

const app = initializeApp(firebaseConfig);
const db = getDatabase(app);

export default async function handler(req, res) {
  if (req.method !== "POST") {
    return res.status(405).json({ message: "Method Not Allowed" });
  }

  const form = formidable({
    maxFileSize: 5 * 1024 * 1024, // เผื่อกลับมาใส่ไฟล์ในอนาคต
  });

  form.parse(req, async (err, fields) => {
    if (err) {
      console.error("❌ error parsing form:", err);
      return res.status(500).json({ message: "เกิดข้อผิดพลาด" });
    }

    const savedData = {
      fullname: fields.fullname,
      phone: fields.phone,
      location: fields.location,
      file: null,
      date: new Date().toISOString(),
    };

    try {
      await push(ref(db, "requests"), savedData);
      console.log("✅ บันทึกข้อมูลสำเร็จ:", savedData);
      res.status(200).json({ message: "✅ ส่งคำขอสำเร็จ" });
    } catch (e) {
      console.error("❌ Firebase Error:", e);
      res.status(500).json({ message: "เกิดข้อผิดพลาดในการบันทึก" });
    }
  });
}
