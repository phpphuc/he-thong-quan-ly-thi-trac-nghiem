import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { FaEdit, FaCheck } from "react-icons/fa";
import {
  IoTrashSharp,
  IoArrowBackOutline,
  IoCheckmarkDone,
} from "react-icons/io5";
import { ShieldX } from "lucide-react";
import DeleteModal from "../common/DeleteModal";
import Notification from "../common/Notification";
import axios from "axios";

const QuestionInfo = ({ id }) => {
  const [questionDetail, setQuestionDetail] = useState(null);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [notification, setNotification] = useState({
    isVisible: false,
    message: "",
  });
  const navigate = useNavigate();

  const handleDeleteClick = () => {
    setIsDeleteModalOpen(true);
  };

  const handleDeleteConfirm = async () => {
    try {
      await axios.delete(`http://127.0.0.1:8000/api/v1/questions/${id}`);
      setNotification({
        isVisible: true,
        message: "Xóa câu hỏi thành công!",
        bgColor: "green",
        icon: <IoCheckmarkDone />, 
      });
      setIsDeleteModalOpen(false);
      navigate(-1);
    } catch (err) {
      console.error("Error deleting question:", err);
      setNotification({
        isVisible: true,
        message: "Đã xảy ra lỗi khi xóa câu hỏi! Hãy thử lại sau.",
        bgColor: "red",
        icon: <ShieldX />, 
      });
      setIsDeleteModalOpen(false);
    }
  };

  useEffect(() => {
    const fetchQuestionDetail = async () => {
      try {
        const response = await axios.get(
          `http://127.0.0.1:8000/api/v1/questions/${id}`
        );
        console.log("Dữ liệu câu hỏi:", response.data);
        setQuestionDetail(response.data.data);
      } catch (err) {
        console.error("Error fetching question detail:", err);
      }
    };

    fetchQuestionDetail();
  }, [id]);

  if (!questionDetail) {
    return <p>Đang tải dữ liệu...</p>;
  }

  return (
    <div className="w-full h-full max-w-4xl mx-auto mt-8 bg-gray-100 px-10 py-5 font-nunito">
      <div className="flex items-center justify-between mb-8">
        <h1 className="text-2xl font-bold">{questionDetail.question}</h1>
        <div className="flex items-center">
          <button
            onClick={() => navigate(`/giangvien/chinhsuacauhoi/${id}`)}
            className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300"
          >
            <FaEdit size={24} />
          </button>
          <button
            onClick={handleDeleteClick}
            className="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 ml-4 rounded-lg transition duration-300"
          >
            <IoTrashSharp size={24} />
          </button>
        </div>
      </div>
      <div className="bg-white p-6 rounded-lg shadow">
        <p><strong>Môn học:</strong> {questionDetail.subject_name}</p>
        <p><strong>Độ khó:</strong> {questionDetail.level}</p>
        <p><strong>Đáp án đúng:</strong> {questionDetail.rightanswer}</p>
        {"ABCD".split('').map((letter) => (
          <p key={letter}>
            <strong>Đáp án {letter}:</strong> {questionDetail[`answer_${letter.toLowerCase()}`]}
          </p>
        ))}
      </div>
      <div className="text-center mt-12">
        <button
          onClick={() => navigate(-1)}
          className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300"
        >
          <div className="flex items-center justify-center">
            <IoArrowBackOutline size={20} className="mr-1" />
            Quay lại
          </div>
        </button>
      </div>
      <DeleteModal
        isOpen={isDeleteModalOpen}
        onClose={() => setIsDeleteModalOpen(false)}
        onConfirm={handleDeleteConfirm}
      />
    </div>
  );
};

export default QuestionInfo;
