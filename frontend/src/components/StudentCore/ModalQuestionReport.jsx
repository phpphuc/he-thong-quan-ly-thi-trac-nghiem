import React, { useState, useEffect } from "react";
import axios from "axios";

const ModalQuestionReport = ({ examId, onClose }) => {
  const [reportData, setReportData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Fetch report data from API
    const fetchReportData = async () => {
      try {
        const response = await axios.get(`http://127.0.0.1:8000/api/v1/results/exam/${examId}/report`);
        setReportData(response.data);
      } catch (error) {
        console.error("Error fetching report data:", error);
      } finally {
        setLoading(false);
      }
    };

    fetchReportData();
  }, [examId]);

  if (loading) {
    return <div>Loading...</div>;
  }

  if (!reportData) {
    return <div>No report available for this exam.</div>;
  }

  return (
    <div className="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
      <div className="bg-white w-1/2 rounded-lg shadow-lg p-6">
        <h2 className="text-xl font-bold mb-4">Báo cáo chi tiết bài thi</h2>
        <table className="w-full border-collapse">
          <thead>
            <tr>
              <th className="border px-4 py-2">STT</th>
              <th className="border px-4 py-2">Câu hỏi</th>
              <th className="border px-4 py-2">Đáp án đúng</th>
              <th className="border px-4 py-2">Đáp án người dùng</th>
              <th className="border px-4 py-2">Kết quả</th>
            </tr>
          </thead>
          <tbody>
            {reportData.map((item, index) => (
              <tr key={index}>
                <td className="border px-4 py-2 text-center">{index + 1}</td>
                <td className="border px-4 py-2">{item.question}</td>
                <td className="border px-4 py-2 text-center">{item.correct_answer}</td>
                <td className="border px-4 py-2 text-center">{item.user_answer}</td>
                <td className="border px-4 py-2 text-center">
                  {item.is_correct ? "Đúng" : "Sai"}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
        <button
          onClick={onClose}
          className="mt-4 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700"
        >
          Đóng
        </button>
      </div>
    </div>
  );
};

export default ModalQuestionReport;
