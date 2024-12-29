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
    teacher_id: 1,
    question: "",
    level: "EASY",
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
    if (!formData.question.trim()) {
      setNotification({
        isVisible: true,
        message: "Vui lòng điền đầy đủ câu hỏi!",
      });
      return;
    }

    if (
      !formData.answer_a.trim() ||
      !formData.answer_b.trim() ||
      !formData.answer_c.trim() ||
      !formData.answer_d.trim()
    ) {
      setNotification({
        isVisible: true,
        message: "Vui lòng điền đầy đủ các phương án trả lời!",
      });
      return;
    }

    if (!formData.rightanswer) {
      setNotification({
        isVisible: true,
        message: "Vui lòng chọn đáp án đúng!",
      });
      return;
    }

    try {
      await axios.post(`http://127.0.0.1:8000/api/v1/questions`, formData);
      setNotification({
        isVisible: true,
        message: "Tạo mới câu hỏi thành công!",
        bgColor: "green",
        icon: <IoCheckmarkDone />,
      });
    } catch (error) {
      setNotification({
        isVisible: true,
        message: "Có lỗi xảy ra khi tạo mới câu hỏi!",
        bgColor: "red",
        icon: <ShieldX />,
      });
      console.log(error);
    }
  };

  return (
    <div className="w-full h-full max-w-4xl mx-auto mt-8 bg-gray-100 px-10 py-5 font-nunito">
      <div className="flex items-center justify-between mb-8">
        <h1 className="text-2xl font-bold">
          Tiêu đề:{" "}
          <input
            type="text"
            value={formData.question}
            placeholder=""
            onChange={(e) =>
              setFormData({ ...formData, title: e.target.value })
            }
            disabled
            className="border border-gray-300 rounded px-2 py-1"
          />
        </h1>
      </div>

      <div className="overflow-x-auto max-h-[500px] bg-white rounded-2xl">
        <div className="px-12 py-6">
          <div className="mb-6">
            <div className="flex items-center justify-between mb-4">
              <div>
                <h2 className="font-semibold">
                  <input
                    type="text"
                    value={formData.question}
                    onChange={(e) =>
                      setFormData({ ...formData, question: e.target.value })
                    }
                    className="border border-gray-300 rounded px-2 py-1 w-96"
                    placeholder="Nhập câu hỏi"
                  />
                </h2>
              </div>
              <div className="mr-4 flex items-center">
                <p className="font-semibold mr-2">Độ khó:</p>
                <select
                  value={formData.level}
                  onChange={(e) =>
                    setFormData({ ...formData, level: e.target.value })
                  }
                  className="border border-gray-300 rounded px-2 py-1"
                >
                  <option value="EASY">EASY</option>
                  <option value="NORMAL">NORMAL</option>
                  <option value="HARD">HARD</option>
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
