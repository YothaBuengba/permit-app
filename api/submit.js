import formidable from "formidable";
import { initializeApp, applicationDefault } from "firebase-admin/app";
import { getDatabase } from "firebase-admin/database";

const app = initializeApp({
  credential: applicationDefault(),
  databaseURL: "https://permit-app-4969b-default-rtdb.firebaseio.com"
});

export const config = {
  api: {
    bodyParser: false,
  },
};

export default function handler(req, res) {
  if (req.method !== "POST") {
    return res.status(405).json({ message: "Method Not Allowed" });
  }

  const form = formidable({ keepExtensions: true });

  form.parse(req, async (err, fields, files) => {
    if (err) {
      console.error("❌ Error parsing form:", err);
      return res.status(500).json({ message: "เกิดข้อผิดพลาดในการส่งข้อมูล" });
    }

    const savedData = {
      fullname: fields.fullname,
      phone: fields.phone,
      location: fields.location,
      timestamp: Date.now()
    };

    try {
      const db = getDatabase();
      await db.ref("requests").push(savedData);
      res.status(200).json({ message: "✅ ส่งคำขอสำเร็จ" });
    } catch (err) {
      console.error("❌ Firebase Error:", err);
      res.status(500).json({ message: "เกิดข้อผิดพลาดในการบันทึก Firebase" });
    }
  });
}
