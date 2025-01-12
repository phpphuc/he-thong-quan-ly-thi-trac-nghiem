import { useNavigate } from "react-router-dom";
import { useAuth } from "../auth/AuthContext";
import homeIcon from '../../assets/home.png';
import examsIcon from '../../assets/exams.png';
import examIcon from '../../assets/exam.png';
import questionIcon from '../../assets/question.png';
import statisticIcon from '../../assets/statistic.png';
import sIcon from '../../assets/s.png';
import cIcon from '../../assets/c.png';

const GeneralSidebar = ({ isSidebarOpen }) => {
  const navigate = useNavigate();
  const { generalCurrentView, setGeneralCurrentView } = useAuth();

  return (
    <div
      className={`fixed text-center h-screen bg-white border-r border-gray-200 transition-transform duration-300 ease-in-out ${
        isSidebarOpen ? "translate-x-0" : "-translate-x-64"
      }`}
      style={{ width: "16rem" }}
    >
      <div className="my-2 mx-2 p-4">
        <div className="flex ">
        <img src={homeIcon} alt="icon" class=" w-7 h-7 mr-2"/>
        <div
          className="text-blue-500 text-xl font-medium mb-14 cursor-pointer"
          onClick={() => navigate("/giangvien")}
        >
          Trắc nghiệm
        </div>
        </div>
        
        <nav>
          <div className="flex ">
            <img src={examsIcon} alt="icon" class="mx-1 w-5 h-5 mr-2"/>
          <button
            onClick={() => {
              setGeneralCurrentView("kythi");
              navigate("/giangvien");
            }}
            className={`mx-2 mb-10 hover:bg-blue-100 rounded-lg ${
              generalCurrentView === "kythi" ? "text-blue-500" : "text-gray-600"
            }`}
          >
            Kỳ thi
          </button>
          </div>
          
          <div className="flex">
          <img src={examIcon} alt="icon" class="mx-1 w-5 h-5 mr-2"/>
          <button
            onClick={() => {
              setGeneralCurrentView("dethi");
              navigate("/giangvien");
            }}
            className={`mx-2 mb-10 hover:bg-blue-100 rounded-lg ${
              generalCurrentView === "dethi" ? "text-blue-500" : "text-gray-600"
            }`}
          >
            Đề thi
          </button>
          </div>
          
          <div className="flex">
          <img src={questionIcon} alt="icon" class="mx-1 w-5 h-5 mr-2"/>
          <button
            onClick={() => {
              setGeneralCurrentView("cauhoi");
              navigate("/giangvien");
            }}
            className={`mx-2 mb-10 hover:bg-blue-100 rounded-lg ${
              generalCurrentView === "cauhoi"
                ? "text-blue-500"
                : "text-gray-600"
            }`}
          >
            Câu hỏi
          </button>
          </div>

          <div className="flex">
          <img src={statisticIcon} alt="icon" class="mx-1 w-5 h-5 mr-2"/>
          <button
            onClick={() => {
              setGeneralCurrentView("thongke");
              navigate("/giangvien");
            }}
            className={`mx-2 mb-10 hover:bg-blue-100 rounded-lg ${
              generalCurrentView === "thongke"
                ? "text-blue-500"
                : "text-gray-600"
            }`}
          >
            Thống kê
          </button>
          </div> 

          <div className="absolute bottom-12 inset-x-0 p-4 border-t flex">
          <img src={sIcon} alt="icon" class="mx-1 w-5 h-5 mr-2"/>
          <p className="mx-2 text-sm text-gray-1000">
            SE100.P13
          </p>
          </div>    

          <div className="absolute bottom-0 inset-x-0 p-4 flex">
          <img src={cIcon} alt="icon" class="mx-1 w-5 h-5 mr-2"/>
          <p className="mx-2 text-sm text-gray-1000">
          Group 13
          </p>
          </div>     
        </nav>
      </div>
    </div>
  );
};

export default GeneralSidebar;
