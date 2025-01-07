import { createContext, useState, useContext } from "react";
import { useNavigate } from "react-router-dom";
import axiosInstance from "../common/axiosInstance";
import PropTypes from "prop-types";

const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [studentCurrentView, setStudentCurrentView] = useState("baithi");
  const [generalCurrentView, setGeneralCurrentView] = useState("kythi");
  const navigate = useNavigate();

  const login = async (email, password) => {
    try {
      const response = await axiosInstance.post("/login", {
        email,
        password,
      });

      // Lưu thông tin user vào localStorage
      localStorage.setItem("user", JSON.stringify(response.data.data));

      // Điều hướng trang theo role tương ứng
      switch (response.data.data.type) {
        case "STUDENT":
          navigate("/sinhvien");
          break;
        case "TEACHER":
          navigate("/giangvien");
          break;
        case "SCHOOLBOARD":
          navigate("/bgh");
          break;
        default:
          break;
      }

      return { success: true };
    } catch (error) {
      console.error("Login failed:", error.response?.data || error.message);
      throw new Error(
        error.response?.data?.message || "Wrong email or password!"
      );
    }
  };

  const logout = async () => {
    try {
      await axiosInstance.post("/logout");
      localStorage.removeItem("user");
      navigate("/", { replace: true });
    } catch (error) {
      if (error.response?.status === 401) {
        // Token đã hết hạn hoặc không hợp lệ
        localStorage.removeItem("user"); // Vẫn xóa data nếu token không hợp lệ
        navigate("/", { replace: true });
      } else {
        console.error("Logout failed:", error);
      }
    }
  };

  return (
    <AuthContext.Provider
      value={{
        login,
        logout,
        studentCurrentView,
        setStudentCurrentView,
        generalCurrentView,
        setGeneralCurrentView,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

AuthProvider.propTypes = {
  children: PropTypes.node.isRequired,
};

export const useAuth = () => useContext(AuthContext);
