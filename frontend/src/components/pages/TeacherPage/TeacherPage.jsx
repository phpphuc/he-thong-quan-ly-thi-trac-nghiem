import { useState } from "react";
import { useAuth } from "../../auth/AuthContext";
import GeneralSidebar from "../../common/GeneralSidebar";
import Examinations from "../../GeneralCore/Examinations";
import TestBank from "../../GeneralCore/TestBank";
import QuestionBank from "../../GeneralCore/QuestionBank";
import Statistics from "../../GeneralCore/Statistics";
import Header from "../../common/Header";

const TeacherPage = () => {
  const [isSidebarOpen, setSidebarOpen] = useState(true);
  const { generalCurrentView } = useAuth();
  const componentMap = {
    kythi: <Examinations />,
    dethi: <TestBank />,
    cauhoi: <QuestionBank />,
    thongke: <Statistics />,
  };

  return (
    <div className="flex h-screen bg-gray-50">
      <GeneralSidebar isSidebarOpen={isSidebarOpen} />
      <div
        className={`flex-1 bg-gray-100 overflow-hidden transition-all duration-300 ease-in-out ${
          isSidebarOpen ? "ml-64" : "ml-0"
        }`}
      >
        <div>
          <Header
            isSidebarOpen={isSidebarOpen}
            setSidebarOpen={setSidebarOpen}
          />
          <div className="bg-gray-100 rounded-lg shadow-sm mb-6 h-[100vh] relative">
            {componentMap[generalCurrentView] || <div>View not found</div>}
          </div>
        </div>
      </div>
    </div>
  );
};

export default TeacherPage;
