const mongoose = require('mongoose');

const bookSchema = mongoose.Schema({
    title: { type: String, required: true },
    description: { type: String, required: true },
    auteur: { type: String, required: true },
});

module.exports = mongoose.model('Books', bookSchema);