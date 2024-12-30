import { useState, useEffect, useCallback } from "react";
import { useNavigate, useLocation, useParams } from "react-router-dom";
import { Clock } from "lucide-react";
import axios from "axios";

const DoTest = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const examData = location.state?.examData;
  const { id } = useParams(); // id bài thi, dùng để load tất cả câu hỏi cho bài thi tương ứng id này
  const [timeLeft, setTimeLeft] = useState(examData?.duration || 2400);
  const [userAnswers, setUserAnswers] = useState([]);
  const [loading, setLoading] = useState(false);

  // Format time from seconds to mm:ss
  const formatTime = (seconds) => {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, "0")}`;
  };

  // Handle submission
  const handleSubmit = useCallback(async () => {
    try {
      setLoading(true);
      const userId = JSON.parse(localStorage.getItem("user"))?.id;

      const requestBody = {
        student_id: userId,
        answers: userAnswers,
      };

      console.log(requestBody);

      // const response = await axios.post(
      //   `http://127.0.0.1:8000/api/v1/exam/${userId}/submit`,
      //   requestBody
      // );

      // if (response.status === 200) {
      //   navigate(-1);
      // }
    } catch (error) {
      console.error("Error submitting exam:", error);
      alert("Có lỗi xảy ra khi nộp bài!");
    } finally {
      setLoading(false);
    }
  }, [userAnswers]);

  // Handle issue report
  const handleReportIssue = async () => {
    alert("Báo lỗi");
  };

  // Handle answer selection
  const handleAnswerSelect = (questionId, answer) => {
    setUserAnswers((prev) => {
      // Find if question already has an answer
      const existingAnswerIndex = prev.findIndex(
        (ans) => ans.question_id === questionId
      );

      // Convert option ID to letter (A, B, C, D)
      const answerLetter = String.fromCharCode(65 + parseInt(answer));

      const newAnswer = {
        question_id: questionId,
        answer: answerLetter,
      };

      if (existingAnswerIndex !== -1) {
        // Update existing answer
        const newAnswers = [...prev];
        newAnswers[existingAnswerIndex] = newAnswer;
        return newAnswers;
      } else {
        // Add new answer
        return [...prev, newAnswer];
      }
    });
  };

  // Countdown timer - starts when component mounts
  useEffect(() => {
    if (timeLeft <= 0) return;

    const timer = setInterval(() => {
      setTimeLeft((prevTime) => {
        if (prevTime <= 1) {
          clearInterval(timer);
          handleSubmit();
          return 0;
        }
        return prevTime - 1;
      });
    }, 1000);

    return () => clearInterval(timer);
  }, [handleSubmit, timeLeft]);

  if (loading) {
    return (
      <div className="w-full h-full flex items-center justify-center">
        <div>Đang xử lý...</div>
      </div>
    );
  }

  return (
    <div className="w-full h-full max-w-4xl mx-auto mt-8 bg-gray-100 px-10 py-5 font-nunito">
      {/* Header */}
      <div className="flex items-center justify-between mb-8">
        <div>
          <h1 className="text-2xl font-bold mb-4">
            Bài thi môn <span>{examData?.subject}</span>
          </h1>
        </div>
        <div className="flex items-center justify-center">
          <div className="text-blue-600 font-bold mr-12 flex items-center">
            <Clock className="w-5 h-5 mr-2" />
            <span>{formatTime(timeLeft)}</span>
          </div>
          <div className="mr-6">
            <button
              onClick={handleSubmit}
              disabled={loading}
              className="w-28 bg-blue-500 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 disabled:opacity-50"
            >
              Nộp bài
            </button>
          </div>
          <div>
            <button
              onClick={handleReportIssue}
              disabled={loading}
              className="w-28 bg-blue-500 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 disabled:opacity-50"
            >
              Báo lỗi
            </button>
          </div>
        </div>
      </div>

      {/* Exam content */}
      <div className="overflow-x-auto max-h-[500px] bg-white rounded-2xl">
        <div className="px-12 py-6">
          {examData?.questions.map((question, index) => (
            <div key={question.id} className="mb-6">
              <div>
                <h2 className="font-semibold mb-4">
                  <span>{index + 1}</span>
                  <span>. {question.content}</span>
                </h2>
              </div>
              <div>
                {question.options.map((option, optionIndex) => (
                  <div key={optionIndex} className="mb-3 flex items-center">
                    <input
                      type="radio"
                      name={`question-${question.id}`}
                      value={optionIndex}
                      checked={
                        userAnswers.find(
                          (ans) => ans.question_id === question.id
                        )?.answer === String.fromCharCode(65 + optionIndex)
                      }
                      onChange={() =>
                        handleAnswerSelect(question.id, optionIndex)
                      }
                      className="cursor-pointer"
                      disabled={loading}
                    />
                    <span className="ml-2">
                      {String.fromCharCode(65 + optionIndex)}. {option.content}
                    </span>
                  </div>
                ))}
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default DoTest;
