// Chỉnh sửa QuestionCreation.jsx
import { useState } from "react";
import { FaCheck } from "react-icons/fa";
import { IoArrowBackOutline, IoCheckmarkDone } from "react-icons/io5";
import { ShieldX } from "lucide-react";
import { IoMdAdd } from "react-icons/io";
import Notification from "../common/Notification";
import axios from "axios";

const QuestionCreation = () => {
  const initialData = {
    subject_id: "1",
    subject_name: "",
    teacher_id: 1,
    question: "",
    level: "Nhận biết",
    rightanswer: "",
    answer_a: "",
    answer_b: "",
    answer_c: "",
    answer_d: "",
  };
  const [formData, setFormData] = useState(initialData);
  const [selectedAnswer, setSelectedAnswer] = useState(null);
  const [notification, setNotification] = useState({
    isVisible: false,
    message: "",
  });

  const handleAnswerChange = (answer) => {
    setSelectedAnswer(answer);
    setFormData({ ...formData, rightanswer: answer });
  };

  const handleSubmit = async () => {
  const validLevels = ["Nhận biết", "Thông hiểu", "Vận dụng"];
  if (!validLevels.includes(formData.level.trim())) {
    setNotification({
      isVisible: true,
      message: "Độ khó không hợp lệ!",
      bgColor: "red",
      icon: <ShieldX />,
    });
    return;
  }
    console.log("Dữ liệu gửi đến API:", formData);

    if (!formData.subject_name.trim()) {
      setNotification({
        isVisible: true,
        message: "Vui lòng nhập tên môn học!",
      });
      return;
    }

    if (!formData.question.trim()) {
      setNotification({
        isVisible: true,
        message: "Vui lòng nhập câu hỏi!",
      });
      return;
    }

    try {
      const response = await axios.post("http://127.0.0.1:8000/api/v1/questions", {
        subject_id: formData.subject_id,
        teacher_id: formData.teacher_id,
        question: formData.question.trim(),
        level: formData.level.trim(),
        rightanswer: formData.rightanswer.trim(),
        answer_a: formData.answer_a.trim(),
        answer_b: formData.answer_b.trim(),
        answer_c: formData.answer_c.trim(),
        answer_d: formData.answer_d.trim(),
      });

      console.log("Phản hồi từ server:", response.data);
      setNotification({
        isVisible: true,
        message: "Tạo mới câu hỏi thành công!",
        bgColor: "green",
        icon: <IoCheckmarkDone />, 
      });
      setFormData(initialData);
      setSelectedAnswer(null);
    } catch (error) {
      console.error("Lỗi khi tạo câu hỏi:", error.response || error.message);
      const errorMessage = error.response?.data?.message || "Đã xảy ra lỗi!";
      setNotification({
        isVisible: true,
        message: errorMessage,
        bgColor: "red",
        icon: <ShieldX />, 
      });
    }
  };

  return (
    <div className="w-full h-full max-w-4xl mx-auto mt-8 bg-gray-100 px-10 py-5 font-nunito">
      <div className="flex items-center justify-between mb-8">
        <h1 className="text-2xl font-bold">Tạo mới câu hỏi</h1>
      </div>

      <div className="overflow-x-auto max-h-[500px] bg-white rounded-2xl">
        <div className="px-12 py-6">
          <div className="mb-6">
            <input
              type="text"
              value={formData.subject_name}
              onChange={(e) =>
                setFormData({ ...formData, subject_name: e.target.value })
              }
              className="border border-gray-300 rounded px-2 py-1 font-semibold mb-6 w-96"
              placeholder="Nhập tên môn học"
            />

            <div className="flex items-center justify-between mb-4">
              <input
                type="text"
                value={formData.question}
                onChange={(e) =>
                  setFormData({ ...formData, question: e.target.value })
                }
                className="border border-gray-300 rounded px-2 py-1 w-96 font-semibold"
                placeholder="Nhập câu hỏi"
              />

              <div className="mr-4 flex items-center">
                <p className="font-semibold mr-2">Độ khó:</p>
                <select
                  value={formData.level}
                  onChange={(e) =>
                    setFormData({ ...formData, level: e.target.value })
                  }
                  className="border border-gray-300 rounded px-2 py-1"
                >
                  <option value="Nhận biết">Nhận biết</option>
                  <option value="Thông hiểu">Thông hiểu</option>
                  <option value="Vận dụng">Vận dụng</option>
                </select>
              </div>
            </div>

            <div>
              {["A", "B", "C", "D"].map((letter) => (
                <div key={letter} className="mb-3 flex items-center">
                  <input
                    type="radio"
                    name="answer"
                    checked={selectedAnswer === letter}
                    onChange={() => handleAnswerChange(letter)}
                  />
                  <input
                    type="text"
                    value={formData[`answer_${letter.toLowerCase()}`]}
                    onChange={(e) => {
                      setFormData({
                        ...formData,
                        [`answer_${letter.toLowerCase()}`]: e.target.value,
                      });
                    }}
                    className="ml-2 w-96 border border-gray-300 rounded px-2 py-1"
                    placeholder={`Phương án ${letter}`}
                  />
                  {selectedAnswer === letter && (
                    <FaCheck size={20} className="text-green-500 ml-2" />
                  )}
                </div>
              ))}
            </div>
          </div>

          <Notification
            message={notification.message}
            isVisible={notification.isVisible}
            onClose={() =>
              setNotification({ ...notification, isVisible: false })
            }
            bgColor={notification.bgColor}
            icon={notification.icon}
          />

          <div className="text-center mt-12 flex items-center justify-center">
            <button
              onClick={() => window.history.back()}
              className="mr-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300"
            >
              <div className="flex items-center justify-center">
                <IoArrowBackOutline size={20} className="mr-1" />
                Quay lại
              </div>
            </button>
            <button
              onClick={handleSubmit}
              className="ml-4 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300"
            >
              <div className="flex items-center justify-center">
                Tạo mới
                <IoMdAdd size={24} className="ml-1 mb-0.5" />
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default QuestionCreation;
