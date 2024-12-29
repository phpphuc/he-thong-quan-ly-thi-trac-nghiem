import { useState, useEffect } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { FaEdit, FaCheck } from "react-icons/fa";
import { IoTrashSharp, IoArrowBackOutline } from "react-icons/io5";
import axios from "axios";

const QuestionInfo = () => {
  const { id } = useParams();
  const [questions, setQuestions] = useState([]);
  const [questionDetail, setQuestionDetail] = useState(null);
  const navigate = useNavigate();

  useEffect(() => {
    const fetchQuestions = async () => {
      try {
        const response = await axios.get(
          "http://127.0.0.1:8000/api/v1/questions"
        );
        setQuestions(response.data);
      } catch (err) {
        console.log(err.message || "Something went wrong");
      }
    };

    fetchQuestions();
  }, []);

  useEffect(() => {
    if (questions.length > 0) {
      // Tìm question với id tương ứng
      const foundQuestion = questions.find(
        (question) => question.id === parseInt(id, 10)
      );
      setQuestionDetail(foundQuestion);
    }
  }, [questions, id]);

  return (
    <div className="w-full h-full max-w-4xl mx-auto mt-8 bg-gray-100 px-10 py-5 font-nunito">
      <div className="flex items-center justify-between mb-8">
        <div>
          <h1 className="text-2xl font-bold">
            {id} - <span>{questionDetail?.question}</span>
          </h1>
        </div>
        <div className="flex items-center justify-center">
          <div className="mr-6">
            <button
              onClick={() => navigate(`/giangvien/chinhsuacauhoi/${id}`)}
              className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300"
            >
              <FaEdit size={24} />
            </button>
          </div>
          <div>
            <button className="bg-blue-500 hover:bg-blue-700 text-white font-bold ml-2 py-2 px-4 rounded-lg transition duration-300">
              <IoTrashSharp size={24} />
            </button>
          </div>
        </div>
      </div>

      {/* Chi tiết câu hỏi */}
      <div className="overflow-x-auto max-h-[500px] bg-white rounded-2xl">
        <div className="px-12 py-6">
          <div className="mb-6">
            <div className="flex items-center justify-between mb-4">
              <div>
                <h2 className="font-semibold">
                  <span>{questionDetail?.question}</span>
                </h2>
              </div>
              <div className="mr-4">
                <p className="font-semibold">
                  Độ khó:{" "}
                  <span className="font-normal mr-2">
                    {questionDetail?.level}
                  </span>
                </p>
              </div>
            </div>
            <div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--4" />
                <span className="ml-2">{questionDetail?.answer_a}</span>
                {questionDetail?.rightanswer === "A" && (
                  <FaCheck size={20} className="text-green-500 ml-2 mb-1" />
                )}
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--4" />
                <span className="ml-2">{questionDetail?.answer_b}</span>
                {questionDetail?.rightanswer === "B" && (
                  <FaCheck size={20} className="text-green-500 ml-2 mb-1" />
                )}
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--4" />
                <span className="ml-2">{questionDetail?.answer_c}</span>
                {questionDetail?.rightanswer === "C" && (
                  <FaCheck size={20} className="text-green-500 ml-2 mb-1" />
                )}
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--4" />
                <span className="ml-2">{questionDetail?.answer_d}</span>
                {questionDetail?.rightanswer === "D" && (
                  <FaCheck size={20} className="text-green-500 ml-2 mb-1" />
                )}
              </div>
            </div>
          </div>

          <div className="text-center mt-12">
            <button
              onClick={() => window.history.back()}
              className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300"
            >
              <div className="flex items-center justify-center">
                <IoArrowBackOutline size={20} className="mr-1" />
                Quay lại
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default QuestionInfo;
