const router = require('express').Router();
const ctrl = require('../controllers/adminPurchaseController.controller');

router.get('/purchases',               ctrl.list);
router.get('/purchases/new',           ctrl.newForm);
router.post('/purchases',              ctrl.createDraft);

router.get('/purchases/:id',           ctrl.detail);
router.post('/purchases/:id/items',    ctrl.addItem);
router.post('/purchases/:id/submit',   ctrl.submit);
router.post('/purchases/:id/receive',  ctrl.receive);
router.post('/purchases/:id/cancel',   ctrl.cancel);

module.exports = router;
