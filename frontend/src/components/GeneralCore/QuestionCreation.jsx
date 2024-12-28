import { useState } from "react";
import { FaCheck } from "react-icons/fa";
import { IoArrowBackOutline } from "react-icons/io5";
import { IoMdAdd } from "react-icons/io";
import Notification from "../common/Notification";

const QuestionCreation = () => {
  const initialData = {
    title: "",
    question: "",
    level: "nhanbiet",
    answers: [
      { id: 1, text: "", isCorrect: false },
      { id: 2, text: "", isCorrect: false },
      { id: 3, text: "", isCorrect: false },
      { id: 4, text: "", isCorrect: false },
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

    // Create the JSON object to send to API
    const dataToSubmit = {
      title: formData.title,
      question: formData.question,
      level: formData.level,
      answers: formData.answers,
    };

    console.log("Data to submit:", dataToSubmit);
    // Gọi API tạo câu hỏi...
  };

  return (
    <div className="w-full h-full max-w-4xl mx-auto mt-8 bg-gray-100 px-10 py-5 font-nunito">
      <div className="flex items-center justify-between mb-8">
        <h1 className="text-2xl font-bold">
          Tiêu đề:{" "}
          <input
            type="text"
            value={formData.title}
            placeholder="Nhập tiêu đề câu hỏi"
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
              <div className="flex-grow mr-4">
                <h2 className="font-semibold">
                  <input
                    type="text"
                    value={formData.question}
                    placeholder="Nhập nội dung câu hỏi"
                    onChange={(e) =>
                      setFormData({ ...formData, question: e.target.value })
                    }
                    className="border border-gray-300 rounded px-2 py-1 w-full"
                  />
                </h2>
              </div>
              <div className="flex items-center">
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
                    placeholder={`Nhập phương án ${String.fromCharCode(
                      65 + index
                    )}`}
                    onChange={(e) => {
                      const newAnswers = [...formData.answers];
                      newAnswers[index].text = e.target.value;
                      setFormData({ ...formData, answers: newAnswers });
                    }}
                    className="ml-2 border border-gray-300 rounded px-2 py-1 w-96"
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
