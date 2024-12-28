import { useState } from "react";
import { FaEdit, FaCheck } from "react-icons/fa";
import { IoArrowBackOutline } from "react-icons/io5";
import Notification from "../common/Notification";

const QuestionAdjustment = () => {
  const initialData = {
    id: "1",
    title: "Tên câu hỏi",
    question: "Hãy cho biết khái niệm OOP là gì?",
    level: "nhanbiet",
    answers: [
      { id: 1, text: "OOP là object oriented programming.", isCorrect: false },
      { id: 2, text: "OOP là object oriented programming.", isCorrect: false },
      { id: 3, text: "OOP là object oriented programming.", isCorrect: false },
      { id: 4, text: "OOP là object oriented programming.", isCorrect: false },
    ],
  };
  const [formData, setFormData] = useState(initialData);
  const [selectedAnswer, setSelectedAnswer] = useState(null);
  const [notification, setNotification] = useState({
    isVisible: false,
    message: "",
  });

  const handleAnswerChange = (index) => {
    const newAnswers = formData.answers.map((answer, i) => ({
      ...answer,
      isCorrect: i === index,
    }));
    setFormData({ ...formData, answers: newAnswers });
    setSelectedAnswer(index);
  };

  const handleSubmit = () => {
    // Kiểm tra các trường dữ liệu đã điền đầy đủ và chọn đáp án đúng chưa
    if (!formData.title.trim() || !formData.question.trim()) {
      setNotification({
        isVisible: true,
        message: `Vui lòng điền đầy đủ tiêu đề và câu hỏi!`,
      });
      return;
    }

    if (formData.answers.some((answer) => !answer.text.trim())) {
      setNotification({
        isVisible: true,
        message: `Vui lòng điền đầy đủ các phương án trả lời!`,
      });
      return;
    }

    if (!formData.answers.some((answer) => answer.isCorrect)) {
      setNotification({
        isVisible: true,
        message: `Vui lòng chọn đáp án đúng!`,
      });
      return;
    }

    // Đóng gói thông tin để gửi qua API
    const dataToSubmit = {
      id: formData.id,
      title: formData.title,
      question: formData.question,
      level: formData.level,
      answers: formData.answers,
    };
    console.log("Data to submit:", dataToSubmit);

    // Gọi API update...
  };

  return (
    <div className="w-full h-full max-w-4xl mx-auto mt-8 bg-gray-100 px-10 py-5 font-nunito">
      <div className="flex items-center justify-between mb-8">
        <h1 className="text-2xl font-bold">
          ID -{" "}
          <input
            type="text"
            value={formData.title}
            onChange={(e) =>
              setFormData({ ...formData, title: e.target.value })
            }
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
                  <option value="nhanbiet">Nhận biết</option>
                  <option value="thonghieu">Thông hiểu</option>
                  <option value="vandung">Vận dụng</option>
                </select>
              </div>
            </div>
            <div>
              {formData.answers.map((answer, index) => (
                <div key={answer.id} className="mb-3 flex items-center">
                  <input
                    type="radio"
                    name="answer"
                    checked={answer.isCorrect}
                    onChange={() => handleAnswerChange(index)}
                  />
                  <input
                    type="text"
                    value={answer.text}
                    onChange={(e) => {
                      const newAnswers = [...formData.answers];
                      newAnswers[index].text = e.target.value;
                      setFormData({ ...formData, answers: newAnswers });
                    }}
                    className="ml-2 w-96 border border-gray-300 rounded px-2 py-1 "
                  />
                  {answer.isCorrect && (
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
              className="ml-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300"
            >
              <div className="flex items-center justify-center">
                Cập nhật
                <FaEdit size={20} className="ml-2 mb-0.5" />
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default QuestionAdjustment;
