// pages/api/submit.js
import { initializeApp } from "firebase/app";
import { getDatabase, ref, push } from "firebase/database";

// ปิด bodyParser = false เพราะเราจะใช้ JSON ปกติ
export const config = {
  api: {
    bodyParser: true,
  },
};

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

  const { fullname, phone, location } = req.body;

  const savedData = {
    fullname,
    phone,
    location,
    date: new Date().toISOString(),
  };

  await push(ref(db, "requests"), savedData);

  res.status(200).json({ message: "✅ ส่งคำขอสำเร็จ" });
}
