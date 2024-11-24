import { TextField } from '@mui/material';

const LoginPage = () => {

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
        <form className="space-y-6">
          {/* Email Field */}
          <TextField
            fullWidth
            label="Email"
            placeholder="Your school email"
            variant="outlined"
            type="email"
          />

          {/* Password Field */}
          <TextField
            fullWidth
            label="Password"
            placeholder="Enter your password"
            variant="outlined"
            type="password"
          />


          {/* Login Button */}
          <button
            type="submit"
            className="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition-colors"
          >
            Login now
          </button>

          {/* Forgot Password */}
          <div className="text-center text-sm text-gray-600">
            Forgot Your Password?{' '}
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