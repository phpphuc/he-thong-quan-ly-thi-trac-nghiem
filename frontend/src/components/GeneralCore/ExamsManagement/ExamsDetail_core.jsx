import { useState, useEffect, useRef, useCallback } from "react";
import { CiFilter } from "react-icons/ci";
import { FaUndo } from "react-icons/fa";
import { MdKeyboardArrowLeft, MdKeyboardArrowRight } from "react-icons/md";
import {
  IoInformationCircle,
  IoTrashSharp,
  IoCheckmarkDone,
} from "react-icons/io5";
import { ShieldX } from "lucide-react";
import { useNavigate } from "react-router-dom";
import DeleteModal from "../../common/DeleteModal";
import Notification from "../../common/Notification";
import "../../../assets/customCSS/LoadingEffect.css";
import axiosInstance from "../../../utils/axiosConfig";

const ExamsDetail_core = () => {

  const [exams, setExams] = useState([]);
  const [isLoading, setIsLoading] = useState(false);
  const [notification, setNotification] = useState({
    isVisible: false,
    message: "",
  });
  const navigate = useNavigate();


  return isLoading ? (
    <div className="loader w-[50px] h-[50px] bg-gray-100 py-5 font-nunito absolute top-1/3 left-1/2 "></div>
  ) : (
    <div className="w-full h-full px-12 mx-auto bg-gray-100 py-5 font-nunito">
      <h1 className="text-2xl font-bold mb-4">ES0001 - SE100.P13</h1>
      <div className="flex justify-between items-center mb-4">
        <h1 className="text-xl font-bold">Loại kỳ thi: Thi riêng</h1>
        <div className="flex justify-end my-5 ">
          <button
            onClick={() => navigate()}
            className="w-35 mr-6 bg-blue-500 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 disabled:opacity-50"
          >
            Chọn loại kỳ thi
          </button>

          <button
            onClick={() => navigate()}
            className="w-30 mr-6 bg-blue-500 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 disabled:opacity-50"
          >
            Thêm đề thi
          </button>

          <button
            onClick={() => navigate()}
            className="w-28 mr-6 bg-blue-500 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 disabled:opacity-50"
          >
            Xóa kỳ thi
          </button>

          <button
            onClick={() => navigate("/giangvien")}
            className="w-28 mr-6 bg-blue-500 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 disabled:opacity-50"
          >
            Quay lại
          </button>
        </div>
      </div>

      <Notification
        message={notification.message}
        isVisible={notification.isVisible}
        onClose={() => setNotification({ ...notification, isVisible: false })}
        bgColor={notification.bgColor}
        icon={notification.icon}
      />

      <div className="overflow-x-auto max-h-[470px] bg-white rounded-2xl">
        <table className="w-full border-collapse">
          <thead>
            <tr className="text-center">
              <th className="px-4 py-2">Mã đề</th>
              <th className="px-4 py-2">Tên bộ đề</th>
              <th className="px-4 py-2">Môn học</th>
              <th className="px-4 py-2">Ngày tạo</th>
              <th className="px-4 py-2">Cấu trúc</th>
              <th className="px-4 py-2 text-center">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            {exams.map((item) => (
              <tr key={item.id} className="border-b">
                <td className="px-4 py-2 text-center">{item.id}</td>
                <td className="px-4 py-2 text-center">{item.question}</td>
                <td className="px-4 py-2 text-center">{item.subject_name}</td>
                <td className="px-4 py-2 text-center">{item.created_at}</td>
                <td className="px-4 py-2 text-center">{item.level}</td>
                <td className="px-4 py-2 text-center">
                  <button
                    className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300"
                  >
                    <IoInformationCircle size={24} />
                  </button>
                  <button
                    className="bg-blue-500 hover:bg-blue-700 text-white font-bold ml-2 py-2 px-4 rounded-lg transition duration-300"
                  >
                    <IoTrashSharp size={24} />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <div className="flex items-center justify-between mt-4">
        <div>
          Hiển thị {exams.length > 0 ? "1" : "0"}-
          {exams.length} trong số {exams.length}
        </div>
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

export default ExamsDetail_core;
