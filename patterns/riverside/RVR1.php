<?php
namespace Freesewing\Patterns\riverside;

use Freesewing\Utils;
use Freesewing\BezierToolbox;

/**
 * RVR1
 *
 * First draft of RVR1
 * Slimfitting Selvedge Jeans. Midrise.
 *
 * @author Stefan Sabatzki <info@riversidedenim.com>
 */
class RVR1 extends \Freesewing\Patterns\Core\Pattern
{
    /*
        ___       _ _   _       _ _
       |_ _|_ __ (_) |_(_) __ _| (_)___  ___
        | || '_ \| | __| |/ _` | | / __|/ _ \
        | || | | | | |_| | (_| | | \__ \  __/
       |___|_| |_|_|\__|_|\__,_|_|_|___/\___|

      Things we need to do before we can draft a pattern
    */

    /**
     * Sets up options and values for our draft
     *
     * By branching this out of the sample/draft methods, we can
     * set a bunch of options and values the influence the draft
     * without having to touch the sample/draft methods
     * When extending this pattern so we can just implement the
     * initialize() method and re-use the other methods.
     *
     * Good to know: 
     * Options are typically provided by the user, but sometimes they are fixed
     * Values are calculated for re-use later
     *
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function initialize($model)
    {
		// Calculated Measurements
        $this->setValue('frontKneeGirth', $model->m('legOpening') / 2);
		$this->setValue('backKneeGirth', (($model->m('legOpening') / 2) + 60));
		$this->setValue('frontLegOpening', (($model->m('legOpening') / 2) - 20));
		$this->setValue('backLegOpening', (($model->m('legOpening') / 2) + 20));
		$this->setValue('rise', $model->m('sideseamLength') - $model->m('inseamLength'));
		$this->setValue('topSideWidth', $model->m('hipGirth') / 4);
		$this->setValue('topSideGapDiameter', (($model->m('hipGirth') / 2 / 10)+10));
		$this->setValue('underSideWidth', (($model->m('hipGirth') / 4)+55));
		$this->setValue('underSideGapDiameter', (($model->m('hipGirth') / 10)+10));
		//$this->setValue('underSideGapDiameter', (($model->m('hipGirth') / 10)+15));
		$this->setValue('waistHeight', (($model->m('hipGirth') / 2 / 10)+30));
		
		$this->msg('Debugval frontKneeGirth: '.$this->v('frontKneeGirth'));
		$this->msg('Debugval backKneeGirth: '.$this->v('backKneeGirth'));
		$this->msg('Debugval rise: '.$this->v('rise'));
		$this->msg('Debugval topSideWidth: '.$this->v('topSideWidth'));
		$this->msg('Debugval topSideGapDiameter: '.$this->v('topSideGapDiameter'));
		$this->msg('Debugval underSideWidth: '.$this->v('underSideWidth'));
		$this->msg('Debugval underSideGapDiameter: '.$this->v('underSideGapDiameter'));
    }

    /*
        ____             __ _
       |  _ \ _ __ __ _ / _| |_
       | | | | '__/ _` | |_| __|
       | |_| | | | (_| |  _| |_
       |____/|_|  \__,_|_|  \__|

      The actual sampling/drafting of the pattern
    */

    /**
     * Generates a draft of the pattern
     *
     * This creates a draft of this pattern for a given model
     * and set of options. You get a complete pattern with
     * all bels and whistles.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draft($model)
    {
        $this->sample($model);
        
        // Finalize all parts
        foreach ($this->parts as $key => $part) {
            $this->{'finalize'.ucfirst($key)}($model);
        }
        
        if ($this->isPaperless) {
            // Finalize all parts
            foreach ($this->parts as $key => $part) {
                $this->{'paperless'.ucfirst($key)}($model);
            }
        }
    }

    /**
     * Generates a sample of the pattern
     *
     * This creates a sample of this pattern for a given model
     * and set of options. You get a barebones pattern with only
     * what it takes to illustrate the effect of changes in
     * the sampled option or measurement.
     *
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function sample($model)
    {
        $this->initialize($model);

        // Draft all parts
        foreach ($this->parts as $key => $part) {
            $this->{'draft'.ucfirst($key)}($model);
        }
    }
	
	public function draftFront($model)
    {
        /** @var Part $p */
        $p = $this->parts['front'];

		$p->newPoint($p->newId('front'), 0, 0, 'Origin');
		$p->addPoint($p->newId('front'), $p->shift('front1',90,$model->m('kneeHeight'), 'Kneeheight'));
		$p->addPoint($p->newId('front'), $p->shift('front1',90,$model->m('inseamLength'), 'InseamLength'));
		$p->addPoint($p->newId('front'), $p->shift('front3',90,$this->v('waistHeight'), 'SideseamLength'));
		$p->addPoint($p->newId('front'), $p->shift('front1',90,$model->m('sideseamLength'), 'SideseamLength'));
		$p->addPoint($p->newId('front'), $p->shift('front4',0,$this->v('topSideWidth'), 'TopSideWidth'));
/*7*/	$p->addPoint($p->newId('front'), $p->shift('front6',0,$this->v('topSideGapDiameter'), 'TopSideGapDiameter'));
		//Kneeline
		$p->addPoint($p->newId('front'), $p->shift('front2',0,$this->v('frontKneeGirth'), 'FrontKneeGirth'));
		//Hemline
		$p->addPoint($p->newId('front'), $p->shift('front1',0,$this->v('frontLegOpening'), 'FrontLegOpening'));
		//Points for inseam-helplines
		$p->newPoint($p->newId('front'), $p->x('front7'), $p->y('front3'), 'Helper');
		$p->addPoint($p->newId('front'), $p->linesCross('front3','front10','front7','front8'));
		//Control-points for curve crotch->knee
/*12*/	$p->clonePoint('front11',$p->newId('front'));
		$p->newPoint($p->newId('front'), $p->x('front8') + $p->deltaX('front8','front11')/4, $p->y('front11') - $p->deltaY('front8','front11')/2, 'Helper');
		//Top of crotch
		$p->newPoint($p->newId('front'), $p->x('front6'), $p->y('front5'), 'Helper');
		$p->newPoint($p->newId('front'), $p->x('front14')-20, $p->y('front5')+10, 'Helper');
		//Control-points for crotch curve
		$p->addPoint($p->newId('front'), $p->shift('front6',$p->angle('front15','front6'),$p->deltaY('front6','front11')/2, 'Helper'));
		$p->newPoint($p->newId('front'), $p->x('front11') - (($p->deltaX('front6','front7')/2)-$p->deltaX('front11','front7')), $p->y('front11'), 'Helper');
		//Top of Sideseam
/*18*/	$p->newPoint($p->newId('front'), $p->x('front15') - $model->m('waistGirth')/4, $p->y('front5')+3, 'Helper');
		//Control-points for curve fly->sideseam
		$p->clonePoint('front18',$p->newId('front'));
		$p->addPoint($p->newId('front'), $p->shift('front15',$p->angle('front15','front6')-90,$p->deltaX('front18','front15')/2, 'Helper'));
		$p->addPoint($p->newId('front'), $p->shift('front20',90,+5, 'Helper'));
		//Control-points for curve at the top of sideseam
		$p->clonePoint('front4',$p->newId('front'));
		$p->newPoint($p->newId('front'), $p->x('front4'), $p->y('front18') + $p->deltaY('front18','front4')/2, 'Helper');
		
		$p->newPath('front', 'M front1 L front2 L front3 L front4 C front22 front23 front18 C front19 front21 front15 L front6 C front16 front17 front11 C front12 front13 front8 L front9 z');
		
		//Save inseam coordinates
		$this->setValue('frontInseamStart_X', $p->x('front11'));
		$this->setValue('frontInseamStart_Y', $p->y('front11'));
		$this->setValue('frontInseamStart_CP_X', $p->x('front12'));
		$this->setValue('frontInseamStart_CP_Y', $p->y('front12'));

		$this->setValue('frontInseamEnd_X', $p->x('front8'));
		$this->setValue('frontInseamEnd_Y', $p->y('front8'));
		$this->setValue('frontInseamEnd_CP_X', $p->x('front13'));
		$this->setValue('frontInseamEnd_CP_Y', $p->y('front13'));
	}
	
	 public function draftBack($model)
    {
        $p = $this->parts['back'];

		$p->newPoint($p->newId('back'), 0, 0, 'Origin');
		$p->addPoint($p->newId('back'), $p->shift('back1',90,$model->m('kneeHeight'), 'Kneeheight'));
		$p->addPoint($p->newId('back'), $p->shift('back1',90,$model->m('inseamLength'), 'InseamLength'));
		$p->addPoint($p->newId('back'), $p->shift('back3',90,$this->v('waistHeight'), 'SideseamLength'));
		$p->addPoint($p->newId('back'), $p->shift('back1',90,$model->m('sideseamLength'), 'SideseamLength'));
		//underSideWidth/underSideGapDiameter
		$p->newPoint($p->newId('back'), $p->x('back4') - $this->v('underSideWidth'), $p->y('back4'), 'Helper');
/*7*/	$p->newPoint($p->newId('back'), $p->x('back6') - $this->v('underSideGapDiameter'), $p->y('back4'), 'Helper');
		//Helpline for Backrise-Angle
		$p->addPoint($p->newId('back'), $p->shift('back4',180-12,$p->distance('back4','back7'), 'Helper'));
		// Find 90° (270 - 12) -> 90 from previous helpline
		$i=1;
		$p->clonePoint('back8',$p->newId('backriseangle'));
        do {
            $p->addPoint($p->newId('backriseangle'), $p->shiftTowards('backriseangle'.$i,'back4',1, 'Helper'));
			$this->msg('Finding Backrise-angle (270-12=258): '.$p->angle('backriseangle'.$i,'back6'));
			$i++;
        } while($p->angle('backriseangle'.$i,'back6')>258);
/*9*/	$p->clonePoint('backriseangle'.$i,$p->newId('back'));	
		//Helplines for waistline
		$p->addPoint($p->newId('back'), $p->shift('back5',$p->angle('back4','back9'),$p->distance('back4','back7'), 'Helper waistline1'));
		$p->addPoint($p->newId('back'), $p->shift('back9',$p->angle('back6','back9'),$p->distance('back3','back5'), 'Helper waistline2'));
		$p->addPoint($p->newId('back'), $p->linesCross('back5','back10','back9','back11'));
		//Kneeline
		$p->addPoint($p->newId('back'), $p->shift('back2',180,$this->v('backKneeGirth'), 'Kneeheight'));
		//Hemline
/*14*/	$p->addPoint($p->newId('back'), $p->shift('back1',180,$this->v('backLegOpening'), 'BackLegOpening'));
		//Draw front inseam 
		$p->newPoint($p->newId('frontis'), $this->v('frontInseamStart_X'), $this->v('frontInseamStart_Y'), 'InseamStart');
		$p->newPoint($p->newId('frontis'), $this->v('frontInseamStart_CP_X'), $this->v('frontInseamStart_CP_Y'), 'InseamStart');
		$p->newPoint($p->newId('frontis'), $this->v('frontInseamEnd_X'), $this->v('frontInseamEnd_Y'), 'InseamStart');
		$p->newPoint($p->newId('frontis'), $this->v('frontInseamEnd_CP_X'), $this->v('frontInseamEnd_CP_Y'), 'InseamStart');
		//Move inseam to kneepoint (-30 is half the additional length in backkneegirth)
		$p->addPoint($p->newId('back'), $p->flipX('frontis1',-30));
		$p->addPoint($p->newId('back'), $p->flipX('frontis2',-30));
		$p->addPoint($p->newId('back'), $p->flipX('frontis3',-30));
		$p->addPoint($p->newId('back'), $p->flipX('frontis4',-30));
		//Adjust top of inseam
/*19*/	$p->newPoint($p->newId('back'), $p->x('back7'), $p->y('back15'), 'Helper');	
		$p->addPoint($p->newId('back'), $p->linesCross('back15','back19','back7','back13'));
		$p->clonePoint('back20',$p->newId('back'));
		//Controlpoints for curve top of sideseam->backseam
/*22*/	$p->addPoint($p->newId('back'), $p->shift('back6',$p->angle('back6', 'back9'),-150, 'Helper'));
		$p->addPoint($p->newId('back'), $p->shift('back20',0,150, 'Helper'));
		$p->addPoint($p->newId('back'), $p->linesCross('back6','back22','back20','back23'));
		$p->clonePoint('back6',$p->newId('back'));
		//Adjust top of sideseam
/*26*/	$p->addPoint($p->newId('back'), $p->shift('back12',$p->angle('back12', 'back5'),$model->m('waistGirth')/4, 'Helper'));
		
		
//		Controlpoint 23 ist gleich 6
//		Controlpoint 22 ist auf Y-Höhe 20 , X = Verlängerung von Gesäßnaht.
//		SVG prüfen
		
		//$p->newPoint($p->newId('back'), $p->x('back20') + $p->deltaX('back20','back6')/2, $p->y('back20'), 'Helper');
		//$p->newPoint($p->newId('back'), $p->x('back6'), $p->y('back6') + $p->deltaY('back6','back20')/2, 'Helper');		
		//$p->addPoint($p->newId('back'), $p->shift('back6',-90,$p->y('back20')/2), 'Control');
		

		$p->newPath('back', 'M back1 L back2 L back3 L back4 L back5 z');
		
		$p->newPath('back1', 'M back4 L back6 L back7');
		
		$p->newPath('back2', 'M back4 L back9');
		
		$p->newPath('back3', 'M back6 L back9');
		
		$p->newPath('back4', 'M back9 L back12');
		
		$p->newPath('back5', 'M back12 L back5');
		
		$p->newPath('back6', 'M back2 L back13');
		
		$p->newPath('back7', 'M back1 L back14');
		
		//sideseam unadjusted = $p->newPath('backtest', 'M back15 C back16 back18 back17');
		
		$p->newPath('back8', 'M back20 C back21 back18 back17');
		
		$p->newPath('back9', 'M back20 C back24 back25 back6');
		
		$p->newPath('back10', 'M back26 L back4');
		
		//Draw front inseam and move it to kneepoint
//		$p->newPoint($p->newId('backis'), $this->v('frontInseamStart_X'), $this->v('frontInseamStart_Y'), 'InseamStart');
//		$p->newPoint($p->newId('backis'), $this->v('frontInseamStart_CP_X'), $this->v('frontInseamStart_CP_Y'), 'InseamStart');
//		$p->newPoint($p->newId('backis'), $this->v('frontInseamEnd_X'), $this->v('frontInseamEnd_Y'), 'InseamStart');
//		$p->newPoint($p->newId('backis'), $this->v('frontInseamEnd_CP_X'), $this->v('frontInseamEnd_CP_Y'), 'InseamStart');
//		
//		$p->addPoint($p->newId('backis'), $p->flipX('backis1',-30));
//		$p->addPoint($p->newId('backis'), $p->flipX('backis2',-30));
//		$p->addPoint($p->newId('backis'), $p->flipX('backis3',-30));
//		$p->addPoint($p->newId('backis'), $p->flipX('backis4',-30));
//		
//		$p->newPath('back8', 'M backis1 C backis2 backis4 backis3');
//		$p->newPath('back9', 'M backis5 C backis6 backis8 backis7');
    }

    /**
     * Drafts the waistband interfacing right part
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftBackPocket($model)
    {
        /** @var Part $p */
        $p = $this->parts['backPocket'];

		$p->newPoint(1, 0, 0, 'Upperleft');
		$p->addPoint(2, $p->shift(1,0,190, 'Upperright'));
		$p->addPoint(3, $p->shift(2,-91.56,155.92, 'Lowerright'));
		$p->addPoint(4, $p->shift(3,-159.75,71.48, 'Center'));
		$p->addPoint(5, $p->shift(4,165.79,97.26, 'Lowerleft'));

		$p->newPath('backPocket1', 'M 1 L 2 L 3 L 4 L 5 z');
    }
	
	 public function draftBackPocketLining($model)
    {
        /** @var Part $p */
        $p = $this->parts['backPocketLining'];

		$p->newPoint(6, 0, 0, 'Upperleft');
		$p->addPoint(7, $p->shift(6,0,190, 'Upperright'));
		$p->addPoint(8, $p->shift(7,-91.56,155.92, 'Lowerright'));
		$p->addPoint(9, $p->shift(8,-159.75,71.48, 'Center'));
		$p->addPoint(10, $p->shift(9,165.79,97.26, 'Lowerleft'));

		$p->newPath('backPocket2', 'M 6 L 7 L 8 L 9 L 10 z');
		$p->paths['backPocket2']->setRender(false);
		
		//offset path is buggy. Don't render, but redraw
		$p->offsetPath('backPocketLiningBase','backPocket2',-10,0, ['class' => 'bp-lining']);
		
		$p->newPath('backPocketLining', 'M intersection-2 L intersection-1 L intersection-3 L intersection-4 L intersection-5 z');
    }
	
    /*
       _____ _             _ _
      |  ___(_)_ __   __ _| (_)_______
      | |_  | | '_ \ / _` | | |_  / _ \
      |  _| | | | | | (_| | | |/ /  __/
      |_|   |_|_| |_|\__,_|_|_/___\___|

      Adding titles/logos/seam-allowance/measurements and so on
    */

    /**
     * Finalizes the waistband interfacing right part
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
	 public function finalizeFront($model)
    {
        /** @var Part $p */
        $p = $this->parts['front'];

        // Title
		$p->addPoint($p->newId('titleAnchor'), $p->shift('front21',270,100, 'titleAnchorFront'));
        $p->addTitle('titleAnchor1', '', $this->t($p->title), 'Compiled 1.0');
    }
	
	  public function finalizeBack($model)
    {
        /** @var Part $p */
        $p = $this->parts['back'];

        // Title
        $p->newPoint('titleAnchor', $p->x('back2')/2, 135);
        $p->addTitle('titleAnchor', '3b', $this->t($p->title), '1x '.$this->t('from interfacing'),'vertical-small');
    }
	
    public function finalizeBackPocket($model)
    {
        /** @var Part $p */
        $p = $this->parts['backPocket'];

        // Title
        $p->newPoint('titleAnchor', $p->x(2)/2, 135);
        $p->addTitle('titleAnchor', '3b', $this->t($p->title), '1x '.$this->t('from interfacing'),'vertical-small');
    }
	
	public function finalizeBackPocketLining($model)
    {
        /** @var Part $p */
        $p = $this->parts['backPocketLining'];

        // Title
        $p->newPoint('titleAnchor', $p->x(7)/2, 135);
        $p->addTitle('titleAnchor', '3b', $this->t($p->title), '1x '.$this->t('from interfacing'),'vertical-small');
    }

}