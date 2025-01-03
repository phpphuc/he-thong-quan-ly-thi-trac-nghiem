import { Routes, Route } from "react-router-dom";
import { AuthProvider } from "./components/auth/AuthContext";
import ProtectedRoute from "./components/auth/ProtectedRoute";
import Login from "./components/pages/LoginPage/Login";
import NotFound from "./components/pages/NotFoundPage/NotFound";

import StudentPage from "./components/pages/StudentPage/StudentPage";
import Test from "./components/pages/StudentPage/Test";
import LookUp from "./components/pages/StudentPage/LookUp";

function App() {
  return (
    <AuthProvider>
      <Routes>
        <Route path="/" element={<Login />} />

        <Route
          path="/sinhvien/*"
          element={
            <ProtectedRoute>
              <Routes>
                <Route path="/" element={<StudentPage />} />
                <Route path="baithi/:id" element={<Test />} />
                <Route path="tracuu/:id" element={<LookUp />} />
              </Routes>
            </ProtectedRoute>
          }
        />

        {/* More routing here ...*/}

        <Route path="*" element={<NotFound />} />
      </Routes>
    </AuthProvider>
  );
}

export default App;
