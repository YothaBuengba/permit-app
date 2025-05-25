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

    const savedData = {
      fullname: fields.fullname?.[0] || "",
      phone: fields.phone?.[0] || "",
      location: fields.location?.[0] || "",
      filename: files.document?.[0]?.newFilename || "",
      originalName: files.document?.[0]?.originalFilename || "",
      timestamp: new Date().toISOString(),
    };

    // ✅ เขียน log ลงไฟล์ permit-log.json
    const logFile = path.join(process.cwd(), "permit-log.json");
    let log = [];

    if (fs.existsSync(logFile)) {
      try {
        const raw = fs.readFileSync(logFile, "utf8");
        log = JSON.parse(raw);
      } catch (err) {
        console.warn("⚠️ อ่าน log ไม่ได้ เริ่มใหม่:", err);
      }
    }

    log.push(savedData);
    fs.writeFileSync(logFile, JSON.stringify(log, null, 2));

    console.log("✅ บันทึกข้อมูลลง permit-log.json แล้ว");
    res.status(200).json({ message: "✅ ส่งคำขอสำเร็จ" });
  });
}
