import React, { useEffect, useState } from "react";
import axios from "axios";
import { CiFilter } from "react-icons/ci";
import { FaUndo } from "react-icons/fa";
import { MdKeyboardArrowLeft, MdKeyboardArrowRight } from "react-icons/md";
import { useNavigate } from "react-router-dom";

const TestHistory = () => {
  const [filterValue, setFilterValue] = useState("default");
  const [typeValue, setTypeValue] = useState("default");
  const [data, setData] = useState([]);
  const navigate = useNavigate();

  useEffect(() => {
    // Fetch test history from API
    const fetchTestHistory = async () => {
      try {
        const response = await axios.get("http://127.0.0.1:8000/api/v1/results/student/{studentId}");
        setData(response.data); // Assumes response is an array of test history objects
      } catch (error) {
        console.error("Error fetching test history:", error);
      }
    };

    fetchTestHistory();
  }, []);

  const handleChangeFilter = (e) => {
    setFilterValue(e.target.value);
  };

  const handleChangeType = (e) => {
    setTypeValue(e.target.value);
  };

  const handleReset = () => {
    setFilterValue("default");
    setTypeValue("default");
  };

  const goToCheck = (testId) => {
    navigate(`/sinhvien/tracuu/${testId}`);
  };

  return (
    <div className="w-full h-full max-w-4xl mx-auto bg-gray-100 px-10 py-5 font-nunito">
      <h1 className="text-2xl font-bold mb-4">Lịch sử bài thi đã làm</h1>

      <div className="flex items-center my-5">
        <div className="flex items-center space-x-4">
          <CiFilter size={24} />
          <span className="font-medium">Lọc theo</span>
          <select
            value={filterValue}
            onChange={handleChangeFilter}
            className="px-2 py-1 border-2 rounded-lg cursor-pointer"
          >
            <option value="default">-- Tất cả --</option>
            <option value="monhoc">Môn học</option>
            <option value="lophoc">Tên bài thi</option>
            <option value="ngaytao">Ngày tạo</option>
          </select>
          <select
            value={typeValue}
            onChange={handleChangeType}
            className="px-2 py-1 border-2 rounded-lg cursor-pointer"
          >
            <option value="default">-- Loại kỳ thi --</option>
            <option value="thirieng">Thi riêng</option>
            <option value="thitaptrung">Thi tập trung</option>
          </select>
          <button
            className="flex items-center justify-center hover:border-red-500 border-2 p-1 rounded-lg"
            onClick={handleReset}
          >
            <FaUndo className="ml-2 text-red-500" />
            <span className="px-2 text-red-500 font-medium">Hoàn tác</span>
          </button>
        </div>
      </div>

      <div className="overflow-x-auto min-h-[470px] bg-white rounded-2xl">
        <table className="w-full border-collapse">
          <thead>
            <tr className="text-center">
              <th className="px-4 py-2">ID</th>
              <th className="px-4 py-2">Tên bài thi</th>
              <th className="px-4 py-2">Môn học</th>
              <th className="px-4 py-2">Ngày tạo</th>
              <th className="px-4 py-2">Loại</th>
              <th className="px-4 py-2 text-center">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            {data.map((item) => (
              <tr key={item.id} className="border-b">
                <td className="px-4 py-2 text-center">{item.id}</td>
                <td className="px-4 py-2 text-center">{item.test_name}</td>
                <td className="px-4 py-2 text-center">{item.subject}</td>
                <td className="px-4 py-2 text-center">{item.create_at}</td>
                <td className="px-4 py-2 text-center">{item.type}</td>
                <td className="px-4 py-2 text-center">
                  <button
                    className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300"
                    onClick={() => goToCheck(item.id)}
                  >
                    Tra cứu
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <div className="flex items-center justify-between mt-4">
        <div>Hiển thị 01-09 trong số {data.length}</div>
        <div className="flex items-center space-x-2">
          <button className="px-3 py-2 rounded hover:bg-gray-200 transition duration-300">
            <MdKeyboardArrowLeft />
          </button>
          <button className="px-3 py-2 rounded hover:bg-gray-200 transition duration-300">
            <MdKeyboardArrowRight />
          </button>
        </div>
      </div>
    </div>
  );
};

export default TestHistory;
