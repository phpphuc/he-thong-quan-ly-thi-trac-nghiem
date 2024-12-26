import { Routes, Route } from "react-router-dom";
import Login from "./components/pages/LoginPage/Login";
import NotFound from "./components/pages/NotFoundPage/NotFound";

import StudentPage from "./components/pages/StudentPage/StudentPage";
import Test from "./components/pages/StudentPage/Test";
import LookUp from "./components/pages/StudentPage/LookUp";
import { AuthProvider } from "./components/auth/AuthContext";

function App() {
  return (
    <AuthProvider>
      <Routes>
        <Route path="/" element={<Login />} />

        <Route
          path="/sinhvien/*"
          element={
            <Routes>
              <Route path="/" element={<StudentPage />} />
              <Route path="baithi/:id" element={<Test />} />
              <Route path="tracuu/:id" element={<LookUp />} />
            </Routes>
          }
        />

        {/* More routing here ...*/}

        <Route path="*" element={<NotFound />}></Route>
      </Routes>
    </AuthProvider>
  );
}

export default App;
