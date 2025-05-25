import formidable from "formidable";
import fs from "fs";
import path from "path";

export const config = {
  api: {
    bodyParser: false,
  },
};

export default async function handler(req, res) {
  if (req.method !== "POST") {
    return res.status(405).json({ message: "Method Not Allowed" });
  }

  const uploadDir = path.join(process.cwd(), "uploads");
  fs.mkdirSync(uploadDir, { recursive: true });

  const form = formidable({
    uploadDir,
    keepExtensions: true,
  });

  form.parse(req, (err, fields, files) => {
    if (err) {
      console.error("❌ Error parsing form:", err);
      return res.status(500).json({ message: "เกิดข้อผิดพลาดในการส่งข้อมูล" });
    }

    // ✅ แสดงผลข้อมูลใน Terminal
    console.log("📌 fields:", fields);
    console.log("📎 files:", files);

    res.status(200).json({ message: "✅ ส่งคำขอสำเร็จ" });
  });
}
