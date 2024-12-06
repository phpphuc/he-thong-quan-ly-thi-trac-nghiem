import { useState } from "react";
import { MdOutlineReport } from "react-icons/md";
import ModalQuestionReport from "./ModalQuestionReport";

const DoTest = () => {
  const [showModal, setShowModal] = useState(false);

  const handleReportClick = () => {
    setShowModal(true);
  };

  return (
    <div className="w-full h-full max-w-4xl mx-auto mt-8 bg-gray-100 px-10 py-5 font-nunito">
      {/* Header */}
      <div className="flex items-center justify-between mb-8">
        <div>
          <h1 className="text-2xl font-bold mb-4">Bài thi môn <span>OOP</span></h1>
        </div>
        <div className="flex items-center justify-center">
          <div className="text-blue-600 font-bold mr-12">Kết quả: <span>9.0</span>/10</div>
          <div className="text-blue-600 font-bold mr-12">Câu đúng: <span>36</span>/40</div>
        </div>
      </div>
      
      {/* Nội dung bài thi */}
      <div className="overflow-x-auto max-h-[550px] bg-white rounded-2xl">
        <div className="px-12 py-6">
          {/* Các câu hỏi và câu trả lời */}
          <div className="mb-6">
            <div className="flex justify-between items-center">
              <h2 className="font-semibold">
                <span>1</span>
                <span>. Hãy cho biết khái niệm OOP là gì?</span>
              </h2>
              <button 
                className="flex items-center bg-blue-500 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300"
                onClick={handleReportClick}
              >
                <MdOutlineReport size={24} className="mr-2"/><span>Báo lỗi</span>
              </button>
            </div>
            <div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--1"/> 
                <span className="ml-2">A. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--1"/>
                <span className="ml-2">B. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--1"/>
                <span className="ml-2">C. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--1"/>
                <span className="ml-2">D. OOP là object oriented programming.</span>
              </div>
            </div>
          </div>

          <div className="mb-6">
            <div className="flex justify-between items-center">
              <h2 className="font-semibold">
                <span>1</span>
                <span>. Hãy cho biết khái niệm OOP là gì?</span>
              </h2>
              <button 
                className="flex items-center bg-blue-500 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300"
                onClick={handleReportClick}
              >
                <MdOutlineReport size={24} className="mr-2"/><span>Báo lỗi</span>
              </button>
            </div>
            <div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--2"/> 
                <span className="ml-2">A. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--2"/>
                <span className="ml-2">B. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--2"/>
                <span className="ml-2">C. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--2"/>
                <span className="ml-2">D. OOP là object oriented programming.</span>
              </div>
            </div>
          </div>

          <div className="mb-6">
            <div className="flex justify-between items-center">
              <h2 className="font-semibold">
                <span>1</span>
                <span>. Hãy cho biết khái niệm OOP là gì?</span>
              </h2>
              <button 
                className="flex items-center bg-blue-500 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300"
                onClick={handleReportClick}
              >
                <MdOutlineReport size={24} className="mr-2"/><span>Báo lỗi</span>
              </button>
            </div>
            <div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--3"/> 
                <span className="ml-2">A. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--3"/>
                <span className="ml-2">B. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--3"/>
                <span className="ml-2">C. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--3"/>
                <span className="ml-2">D. OOP là object oriented programming.</span>
              </div>
            </div>
          </div>

          <div className="mb-6">
            <div className="flex justify-between items-center">
              <h2 className="font-semibold">
                <span>1</span>
                <span>. Hãy cho biết khái niệm OOP là gì?</span>
              </h2>
              <button 
                className="flex items-center bg-blue-500 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300"
                onClick={handleReportClick}
              >
                <MdOutlineReport size={24} className="mr-2"/><span>Báo lỗi</span>
              </button>
            </div>
            <div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--4"/> 
                <span className="ml-2">A. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--4"/>
                <span className="ml-2">B. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--4"/>
                <span className="ml-2">C. OOP là object oriented programming.</span>
              </div>
              <div className="mb-3 flex items-center">
                <input type="radio" name="id__answer--4"/>
                <span className="ml-2">D. OOP là object oriented programming.</span>
              </div>
            </div>
          </div>

          {/* Modal popup báo lỗi */}
          <ModalQuestionReport isOpen={showModal} onClose={() => setShowModal(false)} />
        </div>
      </div>
    </div>
  )
}

export default DoTest