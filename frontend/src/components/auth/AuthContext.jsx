import { createContext, useState, useEffect, useContext } from "react";
import PropTypes from "prop-types";

const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [studentCurrentView, setStudentCurrentView] = useState("baithi");

  useEffect(() => {
    // handle API return from backend
  }, []);

  const login = (userData, token) => {
    // handle login
  };

  const logout = () => {
    // handle logout
  };

  return (
    <AuthContext.Provider
      value={{ user, login, logout, studentCurrentView, setStudentCurrentView }}
    >
      {children}
    </AuthContext.Provider>
  );
};

AuthProvider.propTypes = {
  children: PropTypes.node.isRequired,
};

export const useAuth = () => useContext(AuthContext);
