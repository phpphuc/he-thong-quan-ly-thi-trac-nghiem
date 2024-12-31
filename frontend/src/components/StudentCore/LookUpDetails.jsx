import React, { useState, useEffect } from "react";
import axios from "axios";
import { useParams } from "react-router-dom";

const LookUpDetails = () => {
  const { testId } = useParams();
  const [testDetails, setTestDetails] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Fetch test details from API
    const fetchTestDetails = async () => {
      try {
        const response = await axios.get(`http://127.0.0.1:8000/api/v1/results/${testId}`);
        setTestDetails(response.data);
      } catch (error) {
        console.error("Error fetching test details:", error);
      } finally {
        setLoading(false);
      }
    };

    fetchTestDetails();
  }, [testId]);

  if (loading) {
    return <div>Loading...</div>;
  }

  if (!testDetails) {
    return <div>No details available for this test.</div>;
  }

  return (
    <div className="w-full h-full max-w-4xl mx-auto bg-gray-100 px-10 py-5 font-nunito">
      <h1 className="text-2xl font-bold mb-4">Chi tiết bài thi</h1>
      <div className="bg-white rounded-xl shadow-md p-5">
        <h2 className="text-xl font-semibold mb-3">{testDetails.test_name}</h2>
        <p><strong>Môn học:</strong> {testDetails.subject}</p>
        <p><strong>Ngày tạo:</strong> {testDetails.create_at}</p>
        <p><strong>Loại:</strong> {testDetails.type}</p>
        <p><strong>Kết quả:</strong> {testDetails.result}</p>
        <p><strong>Ghi chú:</strong> {testDetails.notes || "Không có ghi chú"}</p>
      </div>
    </div>
  );
};

export default LookUpDetails;
