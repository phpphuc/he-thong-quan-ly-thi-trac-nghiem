import { useState } from "react";
import axios from "axios";
import { TextField } from "@mui/material";

const LoginPage = () => {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  const handleLogin = async (event) => {
    event.preventDefault(); // Ngăn reload trang khi submit form
    setLoading(true);
    setError("");

    try {
      const response = await axios.post("http://127.0.0.1:8000/api/v1/login", {
        email,
        password,
      });

      // Xử lý thành công
      console.log("Login successful:", response.data);
      alert("Login successful!");
    } catch (err) {
      // Xử lý lỗi
      console.error("Login failed:", err.response?.data || err.message);
      setError(err.response?.data?.message || "Something went wrong.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex flex-col items-center justify-center bg-white p-6">
      {/* Title in blue */}
      <h1 className="text-blue-500 text-2xl font-medium mb-16">
        Hệ thống quản lý thi trắc nghiệm
      </h1>

      {/* Login Container */}
      <div className="w-full max-w-md">
        <h2 className="text-2xl font-bold text-gray-900 mb-8 text-center">
          Login to your account
        </h2>

        {/* Login Form */}
        <form className="space-y-6" onSubmit={handleLogin}>
          {/* Email Field */}
          <TextField
            fullWidth
            label="Email"
            placeholder="Your school email"
            variant="outlined"
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
          />

          {/* Password Field */}
          <TextField
            fullWidth
            label="Password"
            placeholder="Enter your password"
            variant="outlined"
            type="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
          />

          {/* Error Message */}
          {error && (
            <div className="text-red-500 text-sm text-center">{error}</div>
          )}

          {/* Login Button */}
          <button
            type="submit"
            disabled={loading}
            className={`w-full py-2 px-4 rounded transition-colors ${
              loading
                ? "bg-gray-400 cursor-not-allowed"
                : "bg-blue-500 text-white hover:bg-blue-600"
            }`}
          >
            {loading ? "Logging in..." : "Login now"}
          </button>

          {/* Forgot Password */}
          <div className="text-center text-sm text-gray-600">
            Forgot Your Password?{" "}
            <button
              type="button"
              className="text-blue-500 hover:text-blue-600"
            >
              Reset
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default LoginPage;
