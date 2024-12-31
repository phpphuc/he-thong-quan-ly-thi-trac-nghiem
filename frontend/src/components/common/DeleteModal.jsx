const DeleteModal = ({ isOpen, onClose, onConfirm, questionId }) => {
  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div className="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full">
        <h2 className="text-xl font-bold mb-4">Xác nhận xóa</h2>
        <p className="mb-6">Bạn có chắc chắn muốn xóa câu hỏi này không?</p>
        <div className="flex justify-end space-x-4">
          <button
            onClick={onClose}
            className="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-300"
          >
            Hủy
          </button>
          <button
            onClick={() => onConfirm(questionId)}
            className="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-300"
          >
            Xóa
          </button>
        </div>
      </div>
    </div>
  );
};

export default DeleteModal;
