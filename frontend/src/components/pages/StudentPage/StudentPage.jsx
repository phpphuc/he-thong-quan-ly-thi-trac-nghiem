import { useState } from "react";
import TestList from "../../StudentCore/TestList";
import TestHistory from "../../StudentCore/TestHistory";
import StudentSidebar from "../../common/StudentSidebar";
import Header from "../../common/Header";
import { useAuth } from "../../auth/AuthContext";

const StudentPage = () => {
  const [isSidebarOpen, setSidebarOpen] = useState(true);
  const { studentCurrentView } = useAuth();

  return (
    <div className="flex h-screen bg-gray-50">
      <StudentSidebar isSidebarOpen={isSidebarOpen} />
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
          <div className="bg-gray-100 rounded-lg shadow-sm mb-6">
            {studentCurrentView === "tracuu" ? <TestHistory /> : <TestList />}
          </div>
        </div>
      </div>
    </div>
  );
};

export default StudentPage;
