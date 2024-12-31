import { useState, useEffect } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { FaEdit, FaCheck } from "react-icons/fa";
import {
  IoTrashSharp,
  IoArrowBackOutline,
  IoCheckmarkDone,
  IoInformationCircle,
} from "react-icons/io5";
import { ShieldX } from "lucide-react";
import DeleteModal from "../../common/DeleteModal";
import Notification from "../../common/Notification";
import axios from "axios";

const ExamInfo = () => {
  const { id } = useParams();
  const [questions, setQuestions] = useState([]);
  const [questionDetail, setQuestionDetail] = useState(null);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [selectedQuestionId, setSelectedQuestionId] = useState(null);
  const [notification, setNotification] = useState({
    isVisible: false,
    message: "",
  });
  const navigate = useNavigate();

//   const handleDeleteClick = (questionId) => {
//     setSelectedQuestionId(questionId);
//     setIsDeleteModalOpen(true);
//   };

//   const handleDeleteConfirm = async (questionId) => {
//     try {
//       await axios.delete(
//         `http://127.0.0.1:8000/api/v1/questions/${questionId}`
//       );
//       setNotification({
//         isVisible: true,
//         message: "Xóa câu hỏi thành công!",
//         bgColor: "green",
//         icon: <IoCheckmarkDone />,
//       });
//       setIsDeleteModalOpen(false);
//       window.history.back();
//     } catch (err) {
//       console.log(err.message || "Error deleting question");
//       setNotification({
//         isVisible: true,
//         message: "Đã xảy ra lỗi khi xóa câu hỏi! Hãy thử lại sau.",
//         bgColor: "red",
//         icon: <ShieldX />,
//       });
//       setIsDeleteModalOpen(false);
//     }
//   };

//   useEffect(() => {
//     const fetchQuestions = async () => {
//       try {
//         const response = await axios.get(
//           "http://127.0.0.1:8000/api/v1/questions"
//         );
//         setQuestions(response.data);
//       } catch (err) {
//         console.log(err.message || "Something went wrong");
//       }
//     };

//     fetchQuestions();
//   }, []);

//   useEffect(() => {
//     if (questions.length > 0) {
//       // Tìm question với id tương ứng
//       const foundQuestion = questions.find(
//         (question) => question.id === parseInt(id, 10)
//       );
//       setQuestionDetail(foundQuestion);
//     }
//   }, [questions, id]);

  return (
    <div className="w-full h-full max-w-4xl mx-auto mt-8 bg-gray-100 px-10 py-5 font-nunito">
      <div className="flex items-center justify-between mb-8">
        <div>
          <h1 className="text-2xl font-bold">
            {/* {id} - <span>{questionDetail?.question}</span>*/}
            Mã và tên kỳ thi 
          </h1>
        </div>
        <div className="flex items-center justify-center">
          <div className="mr-6">
            <button
            //   onClick={() => navigate(`/giangvien/chinhsuacauhoi/${id}`)}
            onClick={() => navigate(`/giangvien/chinhsuakythi`)}
              className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300"
            >
              <FaEdit size={24} />
            </button>
          </div>
          <div>
            <button
            //   onClick={() => handleDeleteClick(id)}
              className="bg-blue-500 hover:bg-blue-700 text-white font-bold ml-2 py-2 px-4 rounded-lg transition duration-300"
            >
              <IoTrashSharp size={24} />
            </button>
          </div>
        </div>
      </div>

      {/* <Notification
        message={notification.message}
        isVisible={notification.isVisible}
        onClose={() => setNotification({ ...notification, isVisible: false })}
        bgColor={notification.bgColor}
        icon={notification.icon}
      />

      <DeleteModal
        isOpen={isDeleteModalOpen}
        onClose={() => setIsDeleteModalOpen(false)}
        onConfirm={handleDeleteConfirm}
        questionId={selectedQuestionId}
      /> */}

      {/* Chi tiết kỳ thi */}
      <div className="overflow-x-auto max-h-[500px] bg-white rounded-2xl">
        <div className="px-12 py-6">
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
                       {/* {filteredQuestions.map((item) => (
                        <tr key={item.id} className="border-b"> */}
                          {/* <td className="px-4 py-2 text-center">{item.id}</td>
                          <td className="px-4 py-2 text-center">{item.question}</td>
                          <td className="px-4 py-2 text-center">{item.subject_id}</td>
                          <td className="px-4 py-2 text-center">{item.created_at}</td>
                          <td className="px-4 py-2 text-center">{item.level}</td> */}
                          <td className="px-4 py-2 text-center">1</td>
                          <td className="px-4 py-2 text-center">Đề OOP</td>
                          <td className="px-4 py-2 text-center">OOP</td>
                          <td className="px-4 py-2 text-center">01 December 2024</td>
                          <td className="px-4 py-2 text-center">Thi cuối kỳ</td>
                          <td className="px-4 py-2 text-center">
                            <button
                              className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300"
                              // onClick={() => navigate(`/giangvien/cauhoi/${item.id}`)}
                            >
                              <IoInformationCircle size={24} />
                            </button>
                            <button
                              className="bg-blue-500 hover:bg-blue-700 text-white font-bold ml-2 py-2 px-4 rounded-lg transition duration-300"
                              // onClick={() => handleDeleteClick(item.id)}
                            >
                              <IoTrashSharp size={24} />
                            </button>
                          </td>
                        {/* </tr>
                      ))}  */}
                    </tbody>
                  </table>
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

export default ExamInfo;
