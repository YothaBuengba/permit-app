import fs from "fs";
import path from "path";

export default function handler(req, res) {
  const logFile = path.join(process.cwd(), "permit-log.json");

  try {
    const rawData = fs.readFileSync(logFile, "utf8");
    const logs = JSON.parse(rawData);
    res.status(200).json({ logs });
  } catch (err) {
    res.status(500).json({ message: "❌ ไม่สามารถโหลด log ได้", error: err });
  }
}
