import { useNavigate } from "react-router-dom";
import { FaEdit, FaCheck } from "react-icons/fa";
import { IoTrashSharp, IoArrowBackOutline } from "react-icons/io5";

const QuestionInfo = () => {
  const navigate = useNavigate();
  const questionId = "099";

  return (
    <div className="w-full h-full max-w-4xl mx-auto mt-8 bg-gray-100 px-10 py-5 font-nunito">
      <div className="flex items-center justify-between mb-8">
        <div>
          <h1 className="text-2xl font-bold">
            ID - <span>Tên câu hỏi</span>
          </h1>
        </div>
        <div className="flex items-center justify-center">
          <div className="mr-6">
            <button
              onClick={() =>
                navigate(`/giangvien/chinhsuacauhoi/${questionId}`)
              }
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
                  <span>Hãy cho biết khái niệm OOP là gì?</span>
                </h2>
              </div>
              <div className="mr-4">
                <p className="font-semibold">
                  Độ khó: <span className="font-normal mr-2">Nhận biết</span>
                </p>
              </div>
            </div>
            <div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--4" />
                <span className="ml-2">
                  A. OOP là object oriented programming.
                </span>
                <FaCheck size={20} className="text-green-500" />
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--4" />
                <span className="ml-2">
                  B. OOP là object oriented programming.
                </span>
                <FaCheck size={20} className="text-green-500" />
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--4" />
                <span className="ml-2">
                  C. OOP là object oriented programming.
                </span>
                <FaCheck size={20} className="text-green-500" />
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--4" />
                <span className="ml-2">
                  D. OOP là object oriented programming.
                </span>
                <FaCheck size={20} className="text-green-500" />
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
